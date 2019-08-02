<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Entity\Lead;
use AppBundle\Entity\Trade;
use AppBundle\Entity\Account;
use Doctrine\ORM\NoResultException;
use AppBundle\Exception\TradeException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use AppBundle\Exception\FinancialException;
use AppBundle\Exception\OperationException;
use AppBundle\Exception\InsufficientFundsException;

class TradeManager
{
    const START_TRADE_DESCRIPTION = 'Сделка по приобритению лида';

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
     * @param EntityManagerInterface $entityManager
     * @param AccountManager         $accountManager
     * @param HoldManager            $holdManager
     * @param FeesManager            $feesManager
     * @param TransactionManager     $transactionManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        AccountManager $accountManager,
        HoldManager $holdManager,
        FeesManager $feesManager,
        TransactionManager $transactionManager
    ) {
        $this->entityManager = $entityManager;
        $this->accountManager = $accountManager;
        $this->holdManager = $holdManager;
        $this->feesManager = $feesManager;
        $this->transactionManager = $transactionManager;
    }

    /**
     * @param User      $buyer
     * @param User      $seller
     * @param Lead      $lead
     * @param int       $amount
     * @param bool|null $flush
     *
     * @return Trade
     *
     * @throws FinancialException
     * @throws TradeException
     * @throws OperationException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function start(User $buyer, User $seller, Lead $lead, int $amount, ?bool $flush = true): Trade
    {
        if (!$lead->isActive()) {
            throw new TradeException($lead, $buyer, $seller, 'У лида должен быть статус активный для совершения сделки');
        }

        if ($lead->getUser() === $buyer) {
            throw new TradeException($lead, $buyer, $seller, 'Пользователь не может купить лида сам у себя');
        }

        $buyerBalance = $this->accountManager->getAvailableBalance($buyer->getAccount());
        $fee = $this->feesManager->calculateTradeFee($amount, FeesManager::TRADE_BUYER_FEE);

        if ($buyerBalance < $amount + $fee) {
            throw new InsufficientFundsException($buyer->getAccount(), $amount + $fee, 'Недостаточно средств у покупателя');
        }

        $trade = $this->create($buyer, $seller, $lead, $amount);
        $lead->setStatus(Lead::STATUS_RESERVED);

        $hold = $this->holdManager->create($buyer->getAccount(), $trade, $amount + $fee, false);
        $trade->setHold($hold);

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
    public function finishSuccess(Trade $trade, Account $feesAccount): void
    {
        if ($trade->isProcessed()) {
            throw new OperationException($trade, 'Торговая операция уже обработана');
        }

        $buyerAccount = $trade->getBuyerAccount();
        $buyerFee = $this->feesManager->calculateTradeFee($trade->getAmount(), FeesManager::TRADE_BUYER_FEE);

        if ($buyerAccount->getBalance() < $trade->getAmount() + $buyerFee) {
            throw new InsufficientFundsException(
                $buyerAccount,
                $trade->getAmount() + $buyerFee,
                'У покупателя недостаточно средств для завершения сделки'
            );
        }

        $sellerAccount = $trade->getSellerAccount();
        $sellerFee = $this->feesManager->calculateTradeFee($trade->getAmount(), FeesManager::TRADE_SELLER_FEE);

        if ($sellerAccount->getBalance() < $sellerFee) {
            throw new InsufficientFundsException(
                $sellerAccount,
                $sellerFee,
                'У продавца недостаточно средств для завершения сделки'
            );
        }

        $fees = $this->feesManager->createForTrade($trade, false);
        $transactions = [];

        foreach ($fees as $fee) {
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

            if ($trade->hasHold()) {
                $hold = $trade->getHold();
                $trade->setHold(null);
                $em->remove($hold);
            }

            $trade->getLead()->setStatus(Lead::STATUS_SOLD);
            $trade->setStatus(Trade::STATUS_ACCEPTED);

            $em->flush();
        });
    }

    /**
     * @param Trade $trade
     * @param string $status
     *
     * @throws OperationException
     */
    public function finishRejectByLeadStatus(Trade $trade, string $status): void
    {
        if ($trade->isProcessed()) {
            throw new OperationException($trade, 'Торговая операция уже обработана');
        }

        $this->entityManager->transactional(function (EntityManagerInterface $em) use ($trade, $status) {

            if ($trade->hasHold()) {
                $hold = $trade->getHold();
                $trade->setHold(null);
                $em->remove($hold);
            }

            $trade->getLead()->setStatus($status);
            $trade->setStatus(Trade::STATUS_REJECTED);

            $em->flush();
        });
    }

    /**
     * @param User $buyer
     * @param User $seller
     * @param Lead $lead
     * @param int  $amount
     *
     * @return Trade
     */
    private function create(User $buyer, User $seller, Lead $lead, int $amount): Trade
    {
        $trade = new Trade();
        $trade
            ->setBuyer($buyer)
            ->setSeller($seller)
            ->setLead($lead)
            ->setDescription(self::START_TRADE_DESCRIPTION)
            ->setAmount($amount);

        $this->entityManager->persist($trade);

        return $trade;
    }
}