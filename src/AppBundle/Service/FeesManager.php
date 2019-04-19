<?php

namespace AppBundle\Service;

use AppBundle\Entity\Fee;
use AppBundle\Entity\Trade;
use Doctrine\ORM\EntityManagerInterface;

class FeesManager
{
    const TRADE_BUYER_FEE = 10;

    const TRADE_SELLER_FEE = 0;

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
     * @param int   $amount
     * @param float $interest
     *
     * @return int
     */
    public function calculateTradeFee(int $amount, float $interest): int
    {
        return (int)ceil($amount * $interest / 100);
    }

    /**
     * @param Trade     $trade
     * @param bool|null $flush
     *
     * @return Fee[]
     */
    public function createForTrade(Trade $trade, ?bool $flush = true): array
    {
        $fees = [];

        $feeAmount = $this->calculateTradeFee($trade->getAmount(), self::TRADE_BUYER_FEE);

        if ($feeAmount > 0) {
            $fee = new Fee();
            $fee->setOperation($trade)
                ->setPayer($trade->getBuyer())
                ->setDescription('Комиссия на сделку для покупателя')
                ->setAmount($feeAmount);

            $this->entityManager->persist($fee);

            $fees[] = $fee;
        }

        $feeAmount = $this->calculateTradeFee($trade->getAmount(), self::TRADE_SELLER_FEE);

        if ($feeAmount > 0) {
            $fee = new Fee();
            $fee->setOperation($trade)
                ->setPayer($trade->getSeller())
                ->setDescription('Комиссия на сделку для продавца')
                ->setAmount($feeAmount);

            $this->entityManager->persist($fee);

            $fees[] = $fee;
        }

        if ($flush) {
            $this->entityManager->flush();
        }

        return $fees;
    }
}