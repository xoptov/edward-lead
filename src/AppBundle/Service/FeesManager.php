<?php

namespace AppBundle\Service;

use AppBundle\Util\Math;
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
    public function getTradeSellerFee(): float
    {
        return $this->tradeSellerFee;
    }

    /**
     * @param Trade $trade
     * @param bool  $flush
     *
     * @return Fee[]
     */
    public function createForTrade(Trade $trade, bool $flush = true): array
    {
        $fees = [];

        if ($trade->getLead() && $trade->getLead()->getHiddenMargin()) {
            $hiddenMargin = $trade->getLead()->getHiddenMargin();
            $fee = new Fee();
            $fee
                ->setOperation($trade)
                ->setPayer($trade->getBuyer())
                ->setDescription('Наценка системы для покупателя')
                ->setAmount($hiddenMargin);

            $this->entityManager->persist($fee);

            $fees[] = $fee;
        }

        if (isset($hiddenMargin) && $hiddenMargin) {
            $baseAmount = $trade->getAmount() + $hiddenMargin;
        } else {
            $baseAmount = $trade->getAmount();
        }

        $amount = Math::calculateByInterest(
            $baseAmount,
            $this->getCommissionForBuyingLead($trade->getLead())
        );

        if ($amount > 0) {
            $fee = new Fee();
            $fee
                ->setOperation($trade)
                ->setPayer($trade->getBuyer())
                ->setDescription('Комиссия на сделку для покупателя')
                ->setAmount($amount);

            $this->entityManager->persist($fee);

            $fees[] = $fee;
        }

        $amount = Math::calculateByInterest(
            $trade->getAmount(),
            $this->tradeSellerFee
        );

        if ($amount > 0) {
            $fee = new Fee();
            $fee->setOperation($trade)
                ->setPayer($trade->getSeller())
                ->setDescription('Комиссия на сделку для продавца')
                ->setAmount($amount);

            $this->entityManager->persist($fee);

            $fees[] = $fee;
        }

        if ($flush) {
            $this->entityManager->flush();
        }

        return $fees;
    }

    /**
     * Метод возвращает величину комиссии в процентах по комнате.
     *
     * @param Room $room
     *
     * @return float
     */
    public function getCommissionForBuyerInRoom(Room $room): float
    {
        $buyerFee = $room->getBuyerFee();

        if (is_null($buyerFee)) {
            return $this->tradeBuyerFee;
        }

        return $buyerFee;
    }

    /**
     * Метод возвращает величину комиссии в процентах по лиду.
     *
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
}