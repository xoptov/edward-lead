<?php

namespace AppBundle\Service;

use AppBundle\Entity\Account;
use AppBundle\Entity\Operation;
use AppBundle\Exception\AccountException;
use AppBundle\Exception\FinancialException;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\MonetaryTransaction;
use AppBundle\Exception\TransactionException;

class TransactionManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Account   $source
     * @param Account   $destination
     * @param Operation $operation
     * @param bool|null $flush
     *
     * @return array
     *
     * @throws AccountException
     */
    public function create(Account $source, Account $destination, Operation $operation, ?bool $flush = true): array
    {
        if (!$source->isEnabled()) {
            throw new AccountException($source, 'Счёт источник заблокирован и не может участвовать в транзакциях');
        }

        if (!$destination->isEnabled()) {
            throw new AccountException($destination, 'Счёт назначения заблокирован и не может участвовать в транзакциях');
        }

        $outgoing = $this->createOutgoing($source, $operation);
        $this->entityManager->persist($outgoing);

        $income = $this->createIncome($destination, $operation);
        $this->entityManager->persist($income);

        if ($flush) {
            $this->entityManager->flush();
        }

        return [$outgoing, $income];
    }

    /**
     * @param MonetaryTransaction[] $transactions
     *
     * @throws TransactionException
     */
    public function process(array $transactions): void
    {
        foreach ($transactions as $transaction) {
            if ($transaction->isProcessed()) {
                throw new TransactionException($transaction, 'Операция по счёту уже исполнена');
            }

            $account = $transaction->getAccount();
            $account->changeBalance($transaction->getAmount());
            $transaction->setProcessed(true);
        }
    }

    /**
     * @param Account   $account
     * @param Operation $operation
     *
     * @return MonetaryTransaction
     */
    private function createOutgoing(Account $account, Operation $operation): MonetaryTransaction
    {
        $amount = -$operation->getAmount();

        $transaction = new MonetaryTransaction();
        $transaction
            ->setAccount($account)
            ->setOperation($operation)
            ->setAmount($amount);

        return $transaction;
    }

    /**
     * @param Account   $account
     * @param Operation $operation
     *
     * @return MonetaryTransaction
     */
    private function createIncome(Account $account, Operation $operation): MonetaryTransaction
    {
        $transaction = new MonetaryTransaction();
        $transaction
            ->setAccount($account)
            ->setOperation($operation)
            ->setAmount($operation->getAmount());

        return $transaction;
    }
}