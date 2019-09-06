<?php

namespace AppBundle\Service;

use AppBundle\Entity\Lead;
use AppBundle\Entity\User;
use AppBundle\Entity\Trade;
use AppBundle\Util\Formatter;
use AppBundle\Entity\PhoneCall;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

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
    private $defaultLeadCost;

    /**
     * @var int
     */
    private $defaultStarCost;

    /**
     * @var int
     */
    private $leadExpirationPeriod;

    /**
     * @var int
     */
    private $leadPerUser;

    /**
     * @param EntityManagerInterface $entityManager
     * @param TradeManager           $tradeManager
     * @param int                    $leadCost
     * @param int                    $starCost
     * @param int                    $leadExpirationPeriod
     * @param int                    $leadPerUser
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TradeManager $tradeManager,
        int $leadCost,
        int $starCost,
        int $leadExpirationPeriod,
        int $leadPerUser
    ) {
        $this->entityManager = $entityManager;
        $this->tradeManager = $tradeManager;
        $this->defaultLeadCost = $leadCost;
        $this->defaultStarCost = $starCost;
        $this->leadExpirationPeriod = $leadExpirationPeriod;
        $this->leadPerUser = $leadPerUser;
    }

    /**
     * @param Lead     $lead
     * @param null|int $divisor
     *
     * @return int
     */
    public function estimateCost(Lead $lead, ?int $divisor = 1): int
    {
        $room = $lead->getRoom();

        if ($room) {
            if ($room->getLeadPrice()) {
                return $room->getLeadPrice() / $divisor;
            }
        }

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
            $leadCost = $this->defaultLeadCost;
        }

        if (!isset($starCost)) {
            $starCost = $this->defaultStarCost;
        }

        $stars = $this->estimateStars($lead);

        return ($leadCost + $stars * $starCost) / $divisor;
    }

    /**
     * @param Lead $lead
     *
     * @return int
     */
    public function estimateStars(Lead $lead): int
    {
        $stars = 0;

        if ($lead->getName() && $lead->getPhone()) {
            $stars++;
        }

        if ($lead->getChannel() && $lead->getOrderDate()) {
            $stars++;
        }

        if ($lead->isDecisionMaker() !== null) {
            $stars++;
        }

        if ($lead->getInterestAssessment()) {
            $stars++;
        }

        if ($lead->getDescription()) {
            $stars++;
        }

        if ($lead->getAudioRecord()) {
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
     * @param User $user
     *
     * @return bool
     */
    public function checkActiveLeadPerUser(User $user): bool
    {
        try {
            $activeLeadsCount = $this->entityManager
                ->getRepository(Lead::class)
                ->getOwnCount($user);
        } catch(\Exception $e) {
            return false;
        }

        if ($user->getSaleLeadLimit()) {
            $leadPerUser = $user->getSaleLeadLimit();
        } else {
            $leadPerUser = $this->leadPerUser;
        }

        if ($activeLeadsCount >= $leadPerUser) {
            return false;
        }

        return true;
    }

    /**
     * @param Lead $lead
     * @param User $caller
     *
     * @return bool
     *
     * @throws NonUniqueResultException
     */
    public function hasAnsweredPhoneCall(Lead $lead, User $caller): bool
    {
        $phoneCall = $this->entityManager
            ->getRepository(PhoneCall::class)
            ->getAnsweredPhoneCallByLeadAndCaller($lead, $caller);

        return $phoneCall instanceof PhoneCall;
    }

    /**
     * @param Lead $lead
     * @param User $buyer
     *
     * @return bool
     *
     * @throws NonUniqueResultException
     */
    public function hasAcceptedTrade(Lead $lead, User $buyer): bool
    {
        $trade = $this->entityManager
            ->getRepository(Trade::class)
            ->getByLeadAndBuyerAndStatus($lead, $buyer, Trade::STATUS_ACCEPTED);

        return $trade instanceof Trade;
    }

    /**
     * @param Lead $lead
     * @param User $user
     *
     * @return bool
     */
    public function isCanShowPhone(Lead $lead, User $user): bool
    {
        if ($lead->isOwner($user)) {
            return true;
        }

        $room = $lead->getRoom();

        if ($room) {
            if (!$room->isPlatformWarranty()) {
                if ($lead->getBuyer() === $user && ($lead->getStatus() === Lead::STATUS_IN_WORK || $lead->getStatus() === Lead::STATUS_TARGET)) {
                    return true;
                }
            }
        }

        try {
            if ($lead->getBuyer() === $user && ($lead->getStatus() === Lead::STATUS_TARGET || $this->hasAnsweredPhoneCall($lead, $user))) {
                return true;
            }
        } catch (NonUniqueResultException $e) {
            return false;
        }

        return false;
    }

    /**
     * @param Lead $lead
     * @param User $user
     *
     * @return string
     */
    public function getNormalizedPhone(Lead $lead, User $user): string
    {
        if ($this->isCanShowPhone($lead, $user)) {
            return Formatter::humanizePhone($lead->getPhone());
        }

        return Formatter::hidePhoneNumber($lead->getPhone());
    }
}