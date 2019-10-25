<?php

namespace AppBundle\Service;

use Exception;
use AppBundle\Entity\Lead;
use AppBundle\Entity\User;
use Psr\Log\LoggerInterface;
use AppBundle\Util\Formatter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

class LeadManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TimerManager
     */
    private $timerManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

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
    private $leadPerUser;

    /**
     * @param EntityManagerInterface $entityManager
     * @param TimerManager           $timerManager
     * @param LoggerInterface        $logger
     * @param int                    $leadCost
     * @param int                    $starCost
     * @param int                    $leadPerUser
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TimerManager $timerManager,
        LoggerInterface $logger,
        int $leadCost,
        int $starCost,
        int $leadPerUser
    ) {
        $this->entityManager = $entityManager;
        $this->timerManager = $timerManager;
        $this->logger = $logger;
        $this->defaultLeadCost = $leadCost;
        $this->defaultStarCost = $starCost;
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

    /**
     * @param Lead $lead
     */
    public function postCreate(Lead $lead): void
    {
        $lead->setPrice($this->estimateCost($lead));

        if (!$lead->hasRoom()) {
            return;
        }

        $room = $lead->getRoom();

        if ($room->isPlatformWarranty() && $room->isTimer()) {

            try {
                $addedIn24h = $this->entityManager->getRepository(Lead::class)
                    ->getAddedCountInRoomByDate(
                        $room,
                        $this->timerManager->createDateTime()
                    );
            } catch (NonUniqueResultException $e) {
                $this->logger->error($e->getMessage());
                return;
            }

            if ($room->getLeadsPerDay() <= $addedIn24h) {
                return;
            }

            $schedule = $room->getSchedule();

            if (empty($schedule)) {
                return;
            }

            $city = $room->getCity();

            if ($city->getTimezone()) {
                $timezone = new \DateTimeZone($city->getTimezone());
            } else {
                $timezone = new \DateTimeZone('Europe/Moscow');
            }

            $executionHours = $room->getExecutionHours();

            $offsetHours = $this->timerManager->calculateOffsetInHours(
                $schedule,
                $timezone,
                $executionHours
            );

            $timer = $this->timerManager->createTimer($offsetHours);

            $lead->setTimer($timer);
        }
    }
}
