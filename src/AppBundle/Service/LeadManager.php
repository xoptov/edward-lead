<?php

namespace AppBundle\Service;

use AppBundle\Entity\Account;
use AppBundle\Entity\Lead;
use AppBundle\Entity\Trade;
use AppBundle\Entity\User;
use AppBundle\Exception\TradeException;
use Doctrine\ORM\EntityManagerInterface;

class LeadManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TradeManager
     */
    private $tradeManager;

    /**
     * @var int
     */
    private $leadCost;

    /**
     * @var int
     */
    private $starCost;

    /**
     * @var int
     */
    private $leadExpirationPeriod;

    /**
     * @param EntityManagerInterface $entityManager
     * @param TradeManager           $tradeManager
     * @param int                    $leadCost
     * @param int                    $starCost
     * @param int                    $leadExpirationPeriod
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TradeManager $tradeManager,
        int $leadCost,
        int $starCost,
        int $leadExpirationPeriod
    ) {
        $this->entityManager = $entityManager;
        $this->tradeManager = $tradeManager;
        $this->leadCost = $leadCost;
        $this->starCost = $starCost;
        $this->leadExpirationPeriod = $leadExpirationPeriod;
    }

    /**
     * @param Lead     $lead
     * @param null|int $divisor
     *
     * @return int
     */
    public function calculateCost(Lead $lead, ?int $divisor = 1): int
    {
        $city = $lead->getCity();

        if ($city) {
            if ($city->getLeadPrice()) {
                $leadCost = $city->getLeadPrice();
            }
            if ($city->getStarPrice()) {
                $starCost = $city->getStarPrice();
            }
        }

        if (!isset($leadCost)) {
            $leadCost = $this->leadCost;
        }

        if (!isset($starCost)) {
            $starCost = $this->starCost;
        }

        $stars = $this->calculateStars($lead);

        return ($leadCost + $stars * $starCost) / $divisor;
    }

    /**
     * @param Lead $lead
     *
     * @return int
     */
    public function calculateStars(Lead $lead): int
    {
        $stars = 0;

        if ($lead->getPhone() && $lead->getName() && $lead->getCity()) {
            $stars = 1;
        }

        if ($lead->getChannel() && $lead->getOrderDate()) {
            $stars++;
        }

        if ($lead->isDecisionMaker() !== null && $lead->isMadeMeasurement() !== null) {
            $stars++;
        }

        if ($lead->getInterestAssessment()) {
            $stars++;
        }

        if ($lead->getDescription()) {
            $stars++;
        }

        if ($lead->getUploadedAudioRecord()) {
            $stars++;
        }

        return $stars;
    }

    /**
     * @param Lead $lead
     */
    public function setExpirationDate(Lead $lead): void
    {
        $expirationDate = new \DateTime(sprintf('+%d days', $this->leadExpirationPeriod));
        $lead->setExpirationDate($expirationDate);
    }

    /**
     * @param Lead $lead
     * @param User $buyer
     *
     * @throws \Exception
     */
    public function successBuy(Lead $lead, User $buyer): void
    {
        if (!$lead->getTrade() instanceof Trade) {
            throw new \RuntimeException('Для подтверждения сделки необходимо её создать');
        }

        if (!$lead->isReserved()) {
            throw new TradeException($lead, $buyer, $lead->getUser(), 'Лида необходимо зарезервировать перед покупкой');
        }

        if (!$lead->isBuyer($buyer)) {
            throw new TradeException($lead, $buyer, $lead->getUser(), 'Одобрить купленного лида может только покупатель');
        }

        $trade = $lead->getTrade();

        $feeAccount = $this->entityManager->getRepository(Account::class)
            ->getFeesAccount();

        $this->tradeManager->handleSuccess($trade, $feeAccount);
    }
}