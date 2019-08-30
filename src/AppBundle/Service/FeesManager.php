<?php

namespace AppBundle\Service;

use AppBundle\Entity\Fee;
use AppBundle\Entity\Trade;
use Doctrine\ORM\EntityManagerInterface;

class FeesManager
{
    /**
     * @var float
     */
    private $tradeBuyerFee;

    /**
     * @var float
     */
    private $tradeSellerFee;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @param float                  $tradeBuyerFee
     * @param float                  $tradeSellerFee
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        float $tradeBuyerFee,
        float $tradeSellerFee
    ) {
        $this->entityManager = $entityManager;
        $this->tradeBuyerFee = $tradeBuyerFee;
        $this->tradeSellerFee = $tradeSellerFee;
    }

    /**
     * @return float
     */
    public function getTradeBuyerFee(): float
    {
        return $this->tradeBuyerFee;
    }

    /**
     * @return float
     */
    public function getTradeSellerFee(): float
    {
        return $this->tradeSellerFee;
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

        $tradeBuyerFee = $this->tradeBuyerFee;

        $lead = $trade->getLead();

        if ($lead->hasRoom() && $lead->getRoom()->getBuyerFee()) {
            $tradeBuyerFee = $lead->getRoom()->getBuyerFee();
        }

        $feeAmount = $this->calculateTradeFee($trade->getAmount(), $tradeBuyerFee);

        if ($feeAmount > 0) {
            $fee = new Fee();
            $fee->setOperation($trade)
                ->setPayer($trade->getBuyer())
                ->setDescription('Комиссия на сделку для покупателя')
                ->setAmount($feeAmount);

            $this->entityManager->persist($fee);

            $fees[] = $fee;
        }

        $feeAmount = $this->calculateTradeFee($trade->getAmount(), $this->tradeSellerFee);

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