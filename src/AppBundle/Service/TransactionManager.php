<?php

namespace AppBundle\Service;

use AppBundle\Entity\Account;
use AppBundle\Entity\Operation;
use AppBundle\Entity\ClientAccount;
use AppBundle\Event\AccountEvent;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Exception\AccountException;
use AppBundle\Entity\MonetaryTransaction;
use AppBundle\Exception\TransactionException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TransactionManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var int
     */
    private $firstMinimalBound;

    /**
     * @var int
     */
    private $secondMinimalBound;

    /**
     * @param EntityManagerInterface   $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param int                      $firstMinimalBound
     * @param int                      $secondMinimalBound
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        int $firstMinimalBound,
        int $secondMinimalBound
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->firstMinimalBound = $firstMinimalBound;
        $this->secondMinimalBound = $secondMinimalBound;
    }

    /**
     * @param Account   $source
     * @param Account   $destination
     * @param Operation $operation
     * @param bool      $flush
     *
     * @return array
     *
     * @throws AccountException
     */
    public function create(
        Account $source,
        Account $destination,
        Operation $operation,
        bool $flush = true
    ): array {

        if (!$source->isEnabled()) {
            throw new AccountException($source, 'Счёт источник заблокирован и не может участвовать в транзакциях');
        }

        if (!$destination->isEnabled()) {
            throw new AccountException($destination, 'Счёт назначения заблокирован и не может участвовать в транзакциях');
        }

        $result = [];

        $outgoing = $this->createObject(
            $source,
            $operation,
            -$operation->getAmount()
        );

        if ($outgoing) {
            $result[] = $outgoing;
        }

        $income = $this->createObject(
            $destination,
            $operation,
            $operation->getAmount()
        );

        if ($income) {
            $result[] = $income;
        }

        if ($flush) {
            $this->entityManager->flush();
        }

        return $result;
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

            $amountBefore = $account->getBalance();

            $account->changeBalance($transaction->getAmount());
            $transaction->setProcessed(true);

            $amountAfter = $account->getBalance();

            //todo: ну потом куда нибудь перенесём при рефакторинге.

            if ($transaction->isOutcome() && $account instanceof ClientAccount) {
                if (
                    $this->firstMinimalBound < $amountBefore
                    && $this->firstMinimalBound >= $amountAfter
                    && $this->secondMinimalBound < $amountAfter
                ) {
                    $this->eventDispatcher->dispatch(
                        AccountEvent::BALANCE_APPROACHING_ZERO,
                        new AccountEvent($account, $this->firstMinimalBound)
                    );
                }

                if (
                    $this->secondMinimalBound < $amountBefore
                    && $this->secondMinimalBound >= $amountAfter
                ) {
                    $this->eventDispatcher->dispatch(
                        AccountEvent::BALANCE_LOWER_THEN_MINIMAL,
                        new AccountEvent($account, $this->secondMinimalBound)
                    );
                }
            }
        }
    }

    /**
     * @param Account   $account
     * @param Operation $operation
     * @param int       $amount
     * @param bool      $persist
     *
     * @return MonetaryTransaction|null
     */
    private function createObject(
        Account $account,
        Operation $operation,
        int $amount,
        bool $persist = true
    ): ?MonetaryTransaction {

       if (empty($amount)) {
           return null;
       }

        $transaction = new MonetaryTransaction();
        $transaction
            ->setAccount($account)
            ->setOperation($operation)
            ->setAmount($amount);

        if ($persist) {
            $this->entityManager->persist($transaction);
        }

        return $transaction;
    }
}