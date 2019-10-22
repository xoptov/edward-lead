<?php

namespace AppBundle\Entity\Room;

use AppBundle\Entity\City;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Room\Schedule\WorkTime;

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
     * @var City|null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id", nullable=true)
     */
    private $city;

    /**
     * @var WorkTime|null
     *
     * @ORM\Embedded(class="AppBundle\Entity\Room\Schedule\WorkTime")
     */
    private $workTime;

    /**
     * @var int|null
     *
     * @ORM\Column(name="work_days", type="smallint", nullable=true, options={"unsigned":true})
     */
    private $workDays;

    /**
     * @param City $city
     *
     * @return Schedule
     */
    public function setCity(City $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return City|null
     */
    public function getCity(): ?City
    {
        return $this->city;
    }

    /**
     * @param WorkTime $workTime
     *
     * @return Schedule
     */
    public function setWorkTime(WorkTime $workTime): self
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
     * @param int $days
     *
     * @return boolean
     */
    public function setWorkDays(int $days): bool
    {
        if (0 <= $days && 127 >= $days) {
            $this->workDays = $days;

            return true;
        }

        return false;
    }
}
