<?php

namespace AppBundle\Service;

use AppBundle\Entity\Account;
use AppBundle\Entity\Operation;
use AppBundle\Entity\MonetaryHold;
use AppBundle\Entity\ClientAccount;
use Doctrine\ORM\EntityManagerInterface;

class AccountManager
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
     * @param Account $account
     *
     * @return int
     */
    public function calculateBalance(Account $account): int
    {
        $transactions = $this->entityManager
            ->getRepository('AppBundle:MonetaryTransaction')
            ->findBy(['account' => $account]);

        $balance = 0.0;

        foreach ($transactions as $transaction) {
            $balance += $transaction->getAmount();
        }

        return $balance;
    }

    /**
     * @param Account $account
     * @param bool    $flush
     */
    public function recalculateBalance(Account $account, bool $flush = true): void
    {
        $actualBalance = $this->calculateBalance($account);

        if ($account->getBalance() != $actualBalance) {
            $account->setBalance($actualBalance);
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param Account $account
     * @param int     $divisor
     *
     * @return float
     */
    public function getAvailableBalance(Account $account, int $divisor = 1): float
    {
        $balance = $account->getBalance();

        $holds = $this->entityManager
            ->getRepository('AppBundle:MonetaryHold')
            ->findBy(['account' => $account]);


        foreach ($holds as $hold) {
            $balance -= $hold->getAmount();
        }

        return $balance / $divisor;
    }

    /**
     * @param Account $account
     * @param int     $divisor
     *
     * @return float
     */
    public function getHoldAmount(Account $account, int $divisor = 1): float
    {
        $holds = $this->entityManager
            ->getRepository('AppBundle:MonetaryHold')
            ->findBy(['account' => $account]);

        $totalHold = 0.0;

        foreach ($holds as $hold) {
            $totalHold += $hold->getAmount();
        }

        return $totalHold / $divisor;
    }

    /**
     * @param ClientAccount $account
     * @param Operation     $operation
     * @param bool          $flush
     */
    public function setHold(
        ClientAccount $account,
        Operation $operation,
        bool $flush = true
    ): void {

        if ($operation->hasHold()) {
            throw new \RuntimeException('По операции уже производилать блокировка средств');
        }

        $hold = new MonetaryHold();
        $hold->setAccount($account)
            ->setOperation($operation)
            ->setAmount($operation->getAmount());

        $this->entityManager->persist($hold);

        if ($flush) {
            $this->entityManager->flush();
        }
    }
}