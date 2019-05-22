<?php

namespace AppBundle\Service;

use AppBundle\Entity\Lead;

class LeadManager
{
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
     * @param int $leadCost
     * @param int $starCost
     * @param int $leadExpirationPeriod
     */
    public function __construct(
        int $leadCost,
        int $starCost,
        int $leadExpirationPeriod
    ) {
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
}