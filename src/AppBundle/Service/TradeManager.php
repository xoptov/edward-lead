<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Entity\Lead;
use AppBundle\Entity\Trade;
use AppBundle\Entity\Account;
use AppBundle\Exception\TradeException;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Exception\FinancialException;
use AppBundle\Exception\OperationException;
use AppBundle\Exception\InsufficientFundsException;

class TradeManager
{
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
     */
    public function create(User $buyer, User $seller, Lead $lead, int $amount, ?bool $flush = true): Trade
    {
        if ($lead->hasTrade()) {
            throw new TradeException($lead, $buyer, $seller, 'Лид не может быть продан повторно');
        }

        $buyerBalance = $this->accountManager->getAvailableBalance($buyer->getAccount());
        $buyerFee = $this->feesManager->calculateTradeFee($amount, FeesManager::TRADE_BUYER_FEE);

        if ($buyerBalance < $amount + $buyerFee) {
            throw new InsufficientFundsException($buyer->getAccount(), $amount + $buyerFee, 'Недостаточно средств у покапателя');
        }

        $trade = new Trade();
        $trade->setBuyer($buyer)
            ->setSeller($seller)
            ->setLead($lead)
            ->setDescription('Сделка по приобритению лида')
            ->setAmount($amount);

        $this->entityManager->persist($trade);
        $monetaryHold = $this->holdManager->create($buyer->getAccount(), $trade, $amount, false);
        $trade->setHold($monetaryHold);

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
    public function process(Trade $trade, Account $feesAccount): void
    {
        if ($trade->isProcessed()) {
            throw new OperationException($trade, 'Торговая операция уже обработана');
        }

        if (!$trade->isAccepted()) {
            throw new OperationException($trade, 'Торговая операция ещё не одобрена');
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

            $trade->setStatus(Trade::STATUS_DONE);

            $em->flush();
        });
    }

    /**
     * @param Trade $trade
     */
    public function reject(Trade $trade): void
    {
        //todo Необходимо реализовать отклонение сделки.
    }

    /**
     * @param Trade $trade
     */
    public function cancel(Trade $trade): void
    {
        //todo Необходимо реализовать отмену сделки.
    }
}