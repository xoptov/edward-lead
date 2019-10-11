<?php

namespace AppBundle\Service;

use AppBundle\Entity\Fee;
use AppBundle\Entity\User;
use AppBundle\Entity\Lead;
use AppBundle\Entity\Trade;
use Psr\Log\LoggerInterface;
use AppBundle\Entity\Account;
use AppBundle\Entity\PhoneCall;
use AppBundle\Exception\TradeException;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Exception\FinancialException;
use AppBundle\Exception\OperationException;
use AppBundle\Exception\InsufficientFundsException;

class TradeManager
{
    const START_TRADE_DESCRIPTION = 'Сделка по приобритению лида';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var AccountManager
     */
    private $accountManager;

    /**
     * @var HoldManager
     */
    private $holdManager;

    /**
     * @var FeesManager
     */
    private $feesManager;

    /**
     * @var TransactionManager
     */
    private $transactionManager;

    /**
     * @var int
     */
    private $maxAsksCallback;

    /**
     * @param LoggerInterface        $logger
     * @param EntityManagerInterface $entityManager
     * @param AccountManager         $accountManager
     * @param HoldManager            $holdManager
     * @param FeesManager            $feesManager
     * @param TransactionManager     $transactionManager
     * @param int                    $maxAsksCallback
     */
    public function __construct(
        LoggerInterface $logger,
        EntityManagerInterface $entityManager,
        AccountManager $accountManager,
        HoldManager $holdManager,
        FeesManager $feesManager,
        TransactionManager $transactionManager,
        int $maxAsksCallback
    ) {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->accountManager = $accountManager;
        $this->holdManager = $holdManager;
        $this->feesManager = $feesManager;
        $this->transactionManager = $transactionManager;
        $this->maxAsksCallback = $maxAsksCallback;
    }

    /**
     * @param User      $buyer
     * @param User      $seller
     * @param Lead      $lead
     * @param bool|null $flush
     *
     * @return Trade
     *
     * @throws FinancialException
     * @throws TradeException
     */
    public function start(User $buyer, User $seller, Lead $lead, ?bool $flush = true): Trade
    {
        if (!$lead->isExpected()) {
            throw new TradeException($lead, $buyer, $seller, 'У лида должен быть статус активный для совершения сделки');
        }

        if ($lead->isOwner($buyer)) {
            throw new TradeException($lead, $buyer, $seller, 'Пользователь не может купить лида сам у себя');
        }

        $amountForHold = $this->calculateCostWithMarginWithFee($lead);
        $buyerBalance = $this->accountManager->getAvailableBalance($buyer->getAccount());

        if ($buyerBalance < $amountForHold) {
            throw new InsufficientFundsException($buyer->getAccount(), $amountForHold, 'Недостаточно средств у покупателя');
        }

        $trade = $this->create($buyer, $seller, $lead);

        $hold = $this->holdManager->create($buyer->getAccount(), $trade, $amountForHold, false);
        $trade->setHold($hold);

        $fees = $this->feesManager->createForTrade($trade, false);

        foreach ($fees as $fee) {
            $trade->addFee($fee);
        }

        $lead->setStatus(Lead::STATUS_IN_WORK);

        if ($flush) {
            $this->entityManager->flush();
        }

        return $trade;
    }

    /**
     * @param Trade   $trade
     * @param Account $feesAccount
     *
     * @throws FinancialException
     * @throws OperationException
     */
    public function accept(Trade $trade, Account $feesAccount): void
    {
        if ($trade->isProcessed()) {
            throw new OperationException($trade, 'Торговая операция уже обработана');
        }

        $transactions = [];

        /** @var Fee $fee */
        foreach ($trade->getFees() as $fee) {
            if ($fee->isProcessed()) {
                continue;
            }
            $transactions = array_merge(
                $transactions,
                $this->transactionManager->create($fee->getPayerAccount(), $feesAccount, $fee, false)
            );
        }

        $transactions = array_merge($transactions, $this->transactionManager->create(
            $trade->getBuyerAccount(),
            $trade->getSellerAccount(),
            $trade,
            false
        ));

        $this->entityManager->transactional(function(EntityManagerInterface $em) use ($trade, $transactions) {

            $this->transactionManager->process($transactions);

            /** @var Fee $fee */
            foreach ($trade->getFees() as $fee) {
                $fee->setStatus(Fee::STATUS_PROCESSED);
            }

            $trade->setStatus(Trade::STATUS_ACCEPTED);
            $trade->getLead()->setStatus(Lead::STATUS_TARGET);

            if ($trade->hasHold()) {
                $hold = $trade->getHold();
                $trade->setHold(null);
                $em->remove($hold);
            }

            $em->flush();
        });
    }

    /**
     * @param Trade $trade
     *
     * @throws OperationException
     */
    public function reject(Trade $trade): void
    {
        if ($trade->isProcessed()) {
            throw new OperationException($trade, 'Торговая операция уже обработана');
        }

        $this->entityManager->transactional(function (EntityManagerInterface $em) use ($trade) {

            $lead = $trade->getLead();

            $trade->setStatus(Trade::STATUS_REJECTED);
            $lead->setStatus(Lead::STATUS_NOT_TARGET);

            /** @var Fee $fee */
            foreach ($trade->getFees() as $fee) {
                $this->entityManager->remove($fee);
                $trade->removeFee($fee);
            }

            if ($trade->hasHold()) {
                $hold = $trade->getHold();
                $trade->setHold(null);
                $em->remove($hold);
            }

            $em->flush();
        });
    }

    /**
     * @param Trade $trade
     *
     * @throws OperationException
     */
    public function arbitrage(Trade $trade): void
    {
        if ($trade->isProcessed()) {
            throw new OperationException($trade, 'Торговая операция уже обработана');
        }

        $this->entityManager->transactional(function (EntityManagerInterface $em) use ($trade) {

            $lead = $trade->getLead();
            $trade->setStatus(Trade::STATUS_PROCEEDING);
            $lead->setStatus(Lead::STATUS_ARBITRATION);

            $em->flush();
        });
    }

    /**
     * @param Trade $trade
     * @param bool  $flush
     *
     * @throws OperationException
     */
    public function askCallback(Trade $trade, bool $flush = true): void
    {
        if ($trade->isProcessed()) {
            throw new OperationException($trade, 'Торговая операция уже обработана');
        }

        if ($trade->getAskCallbackCount() >= $this->maxAsksCallback) {
            throw new OperationException($trade, 'Нельзя более 2-х раз указывать "просил перезвонить" для одного лида');
        }

        $phoneCall = $trade->getLastPhoneCall();

        if (!$trade->addAskCallbackPhoneCall($phoneCall)) {
            throw new OperationException($trade, 'Последний телефонный звонок лиду уже отмечен как "просил перезвонить"');
        }

        $trade->setStatus(Trade::STATUS_CALL_BACK);

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param Account   $feesAccount
     * @param Trade     $trade
     * @param \DateTime $staleTimeBound
     */
    public function autoFinish(Trade $trade, Account $feesAccount, \DateTime $staleTimeBound): void
    {
        $phoneCall = $trade->getLastPhoneCall();

        if (!$phoneCall) {
            if ($trade->getCreatedAt() < $staleTimeBound) {
                try {
                    $this->accept($trade, $feesAccount);
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                }
            }
            return;
        }

        if ($trade->getStatus() === Trade::STATUS_CALL_BACK) {
            if ($phoneCall->getCreatedAt() < $staleTimeBound) {
                try {
                    $this->accept($trade, $feesAccount);
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                }
                return;
            }
            if ($trade->getAskCallbackCount() >= $this->maxAsksCallback) {
                try {
                    $this->reject($trade);
                } catch (OperationException $e) {
                    $this->logger->error($e->getMessage());
                }
            }
            return;
        }

        if ($phoneCall->getResult() === PhoneCall::RESULT_SUCCESS) {
            if ($trade->getCreatedAt() < $staleTimeBound) {
                try {
                    $this->accept($trade, $feesAccount);
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                }
            }
            return;
        }

        if ($trade->getCreatedAt() < $staleTimeBound) {
            try {
                $this->reject($trade);
            } catch (OperationException $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }

    /**
     * @param Trade $trade
     *
     * @return bool
     */
    public function isCanShowResultModal(Trade $trade): bool
    {
        $lastPhoneCall = $trade->getLastPhoneCall();

        if ($lastPhoneCall
            && $lastPhoneCall->isResultSuccess()
            && !$trade->hasAskCallbackPhoneCall($lastPhoneCall)
        ) {
            return true;
        }

        return false;
    }

    /**
     * Метод для расчёта стоимости с учётом комиссии и без учёта наценки.
     *
     * @param Lead $lead
     *
     * @return int
     */
    public function calculateCostWithFee(Lead $lead): int
    {
        $interest = $this->feesManager->getCommissionForBuyingLead($lead);

        if ($interest) {
            return $lead->getPrice() + FeesManager::calculateFee($lead->getPrice(), $interest);
        }

        return $lead->getPrice();
    }

    /**
     * Метод для расчёта стоимости с учётом наценки и комиссии.
     *
     * @param Lead $lead
     *
     * @return int
     */
    public function calculateCostWithMarginWithFee(Lead $lead): int
    {
        $leadPrice = $lead->getPriceWithMargin();

        $interest = $this->feesManager->getCommissionForBuyingLead($lead);

        if ($interest) {
            return $leadPrice + FeesManager::calculateFee($leadPrice, $interest);
        }

        return $leadPrice;
    }

    /**
     * @param User $buyer
     * @param User $seller
     * @param Lead $lead
     *
     * @return Trade
     */
    private function create(User $buyer, User $seller, Lead $lead): Trade
    {
        $trade = new Trade();
        $trade
            ->setBuyer($buyer)
            ->setSeller($seller)
            ->setLead($lead)
            ->setDescription(self::START_TRADE_DESCRIPTION)
            ->setAmount($lead->getPrice());

        $this->entityManager->persist($trade);

        return $trade;
    }
}