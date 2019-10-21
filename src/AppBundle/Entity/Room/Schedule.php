<?php

namespace AppBundle\Entity\Room;

use AppBundle\Entity\City;
use AppBundle\Entity\Room\Schedule\WorkTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Schedule
{
    const MONDAY    = 1;
    const TUESDAY   = 2;
    const WEDNESDAY = 3;
    const THURSDAY  = 4;
    const FRIDAY    = 5;
    const SATURDAY  = 6;
    const SUNDAY    = 7;

    /**
     * @var City
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id", nullable=true)
     */
    private $city;

    /**
     * @var WorkTime
     *
     * @ORM\Embedded(class="AppBundle\Entity\Room\Schedule\WorkTime")
     */
    private $workTime;

    /**
     * @var array
     *
     * @ORM\Column(name="days_of_week", type="simple_array", nullable=true)
     */
    private $daysOfWeek = array();

    /**
     * @var int
     *
     * @ORM\Column(name="execution_hours", type="smallint", nullable=true)
     */
    private $executionHours;

    /**
     * @var int
     *
     * @ORM\Column(name="leads_per_day", type="smallint", nullable=true)
     */
    private $leadsPerDay;

    /**
     * @param array $daysOfWeek
     */
    public function __construct(array $daysOfWeek = array())
    {
        foreach ($daysOfWeek as $day)
        {
            $this->addDayOfWeek($day);
        }
    }

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
     * @return City
     */
    public function getCity(): City
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
        if (in_array($day, [
            self::MONDAY,
            self::TUESDAY,
            self::WEDNESDAY,
            self::THURSDAY,
            self::FRIDAY,
            self::SATURDAY,
            self::SUNDAY])
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getDaysOfWeek(): array
    {
        return $this->daysOfWeek;
    }

    /**
     * @param int $day
     *
     * @return bool
     */
    public function addDayOfWeek(int $day): bool
    {
        if (!$this->isDayOfWeek($day)) {
            return false;
        }

        if (in_array($day, $this->daysOfWeek)) {
            return false;
        }

        $this->daysOfWeek[] = $day;

        return sort($this->daysOfWeek);
    }

    /**
     * @param int $day
     *
     * @return bool
     */
    public function removeDayOfWeek(int $day): bool
    {
       if (!in_array($day, $this->daysOfWeek)) {
           return false;
       }

       $key = array_search($day, $this->daysOfWeek);

       if (!$key) {
           return false;
       }

       unset($this->daysOfWeek[$key]);

       return true;
    }

    /**
     * @param int $hours
     *
     * @return Schedule
     */
    public function setExecutionHours(int $hours): self
    {
        $this->executionHours = $hours;

        return $this;
    }

    /**
     * @return int
     */
    public function getExecutionHours(): int
    {
        return $this->executionHours;
    }

    /**
     * @param int $limit
     *
     * @return Schedule
     */
    public function setLeadsPerDay(int $limit): self
    {
        $this->leadsPerDay = $limit;

        return $this;
    }

    /**
     * @return int
     */
    public function getLeadsPerDay(): int
    {
        return $this->leadsPerDay;
    }
}