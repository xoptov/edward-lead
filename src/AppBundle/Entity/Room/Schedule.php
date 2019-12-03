<?php

namespace AppBundle\Entity\Room;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Room\Schedule\WorkTime;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
class Schedule
{
    const MONDAY    = 1;
    const TUESDAY   = 2;
    const WEDNESDAY = 4;
    const THURSDAY  = 8;
    const FRIDAY    = 16;
    const SATURDAY  = 32;
    const SUNDAY    = 64;

    /**
     * @var WorkTime|null
     *
     * @ORM\Embedded(class="AppBundle\Entity\Room\Schedule\WorkTime")
     */
    private $workTime;

    /**
     * @var int|null
     *
     * @Assert\Range(min=1, max=127)
     *
     * @ORM\Column(name="work_days", type="smallint", nullable=true, options={"unsigned":true})
     */
    private $workDays;

    /**
     * @param WorkTime|null $workTime
     *
     * @return Schedule
     */
    public function setWorkTime(?WorkTime $workTime): self
    {
        $this->workTime = $workTime;

        return $this;
    }

    /**
     * @return WorkTime|null
     */
    public function getWorkTime(): ?WorkTime
    {
        return $this->workTime;
    }

    /**
     * @param int $day
     *
     * @return bool
     */
    public function isDayOfWeek(int $day): bool
    {
        return in_array($day, [
            self::MONDAY,
            self::TUESDAY,
            self::WEDNESDAY,
            self::THURSDAY,
            self::FRIDAY,
            self::SATURDAY,
            self::SUNDAY
        ]);
    }

    /**
     * @return int|null
     */
    public function getWorkDays(): ?int
    {
        return $this->workDays;
    }

    /**
     * @param int|null $days
     *
     * @return boolean
     */
    public function setWorkDays(?int $days): bool
    {
        if (empty($days)) {
            return false;
        }

        if (0 <= $days && 127 >= $days) {
            $this->workDays = $days;

            return true;
        }

        return false;
    }

    /**
     * @return array|null
     */
    public function getMatrix(): ?array
    {
        if (!$this->workTime) {
            return null;
        }

        if (!$this->workDays) {
            return null;
        }

        $startHour = (int)$this->workTime->getStartAt()->format('H');
        $endHour = (int)$this->workTime->getEndAt()->format('H');

        $matrix = [];

        for ($dowBit = 1, $dow = 1; $dowBit <= self::SUNDAY; $dowBit *= 2, $dow++) {
            for ($hour = 0; $hour < 24; $hour++) {
                if (!($this->workDays & $dowBit)) {
                    $matrix[$dow][$hour] = 0;
                    continue;
                }

                if ($startHour === $endHour && $hour === $startHour) {
                    $matrix[$dow][$hour] = 1;
                    continue;
                }

                if ($startHour < $endHour
                    && ($hour >= $startHour && $hour < $endHour)
                ) {
                    $matrix[$dow][$hour] = 1;
                    continue;
                }

                if ($startHour > $endHour
                    && ($hour >= $startHour || $hour < $endHour)) {
                    $matrix[$dow][$hour] = 1;
                    continue;
                }

                $matrix[$dow][$hour] = 0;
            }
        }

        return $matrix;
    }
}
