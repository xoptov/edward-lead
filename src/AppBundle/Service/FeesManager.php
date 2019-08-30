<?php

namespace AppBundle\Service;

use AppBundle\Entity\Fee;
use AppBundle\Entity\Lead;
use AppBundle\Entity\Room;
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

        $feeAmount = $this->calculateTradeFee(
            $trade->getAmount(),
            $this->getCommissionForBuyingLead($trade->getLead())
        );

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

    /**
     * @param Room $room
     *
     * @return float
     */
    public function getCommissionForBuyerInRoom(Room $room): float
    {
        if ($room->getBuyerFee()) {
            return $room->getBuyerFee();
        }

        return $this->tradeBuyerFee;
    }

    /**
     * @param Lead $lead
     *
     * @return float
     */
    public function getCommissionForBuyingLead(Lead $lead): float
    {
        if ($lead->hasRoom()) {
            return $this->getCommissionForBuyerInRoom($lead->getRoom());
        }

        return $this->tradeBuyerFee;
    }

    /**
     * @param Lead $lead
     *
     * @return int
     */
    public function getLeadPriceWithBuyerFee(Lead $lead): int
    {
        return (int)ceil($lead->getPrice() + $lead->getPrice() * $this->getCommissionForBuyingLead($lead) / 100);
    }
}