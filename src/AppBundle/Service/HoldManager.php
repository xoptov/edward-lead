<?php

namespace AppBundle\Service;

use AppBundle\Entity\Operation;
use AppBundle\Entity\MonetaryHold;
use AppBundle\Entity\ClientAccount;
use Doctrine\ORM\EntityManagerInterface;

class HoldManager
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
     * @param ClientAccount $account
     * @param Operation     $operation
     * @param int           $amount
     * @param bool|null     $flush
     *
     * @return MonetaryHold
     */
    public function create(ClientAccount $account, Operation $operation, int $amount, ?bool $flush = true): MonetaryHold
    {
        $monetaryHold = new MonetaryHold();
        $monetaryHold
            ->setAccount($account)
            ->setOperation($operation)
            ->setAmount($amount);

        $this->entityManager->persist($monetaryHold);

        if ($flush) {
            $this->entityManager->flush();
        }

        return $monetaryHold;
    }
}