<?php

namespace AppBundle\Service;

use Exception;
use AppBundle\Entity\Lead;
use AppBundle\Entity\User;
use AppBundle\Util\Formatter;
use Doctrine\ORM\EntityManagerInterface;

class LeadManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

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
     * @param int                    $leadCost
     * @param int                    $starCost
     * @param int                    $leadExpirationPeriod
     * @param int                    $leadPerUser
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        int $leadCost,
        int $starCost,
        int $leadExpirationPeriod,
        int $leadPerUser
    ) {
        $this->entityManager = $entityManager;
        $this->defaultLeadCost = $leadCost;
        $this->defaultStarCost = $starCost;
        $this->leadExpirationPeriod = $leadExpirationPeriod;
        $this->leadPerUser = $leadPerUser;
    }

    /**
     * @param Lead $lead
     * @param User $user
     *
     * @return bool
     */
    public static function isCanShowPhone(Lead $lead, User $user): bool
    {
        if ($lead->isOwner($user)) {
            return true;
        }

        if (!$lead->hasTrade()) {
            return false;
        }

        $trade = $lead->getTrade();

        if (!$trade->isBuyer($user)) {
            return false;
        }

        if ($trade->isAccepted()) {
            return true;
        }

        if ($trade->isNew() && !$lead->isPlatformWarranty()) {
            return true;
        }

        return false;
    }

    /**
     * @param Lead     $lead
     * @param null|int $divisor
     *
     * @return int
     */
    public function estimateCost(Lead $lead, ?int $divisor = null): int
    {
        $room = $lead->getRoom();

        if ($room) {
            if ($room->hasLeadPrice()) {
                return $room->getLeadPrice($divisor);
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

        if ($divisor) {
            return ($leadCost + $stars * $starCost) / $divisor;
        }

        return $leadCost + $stars * $starCost;
    }

    /**
     * @param Lead $lead
     *
     * @return int
     */
    public function estimateStars(Lead $lead): int
    {
        $stars = 0;

        if ($lead->getPhone()) {
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
     *
     * @throws Exception
     *
     * @todo тут необходимо переделать с дней на часы когда я буду использовать таймеры.
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
        } catch(Exception $e) {
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
