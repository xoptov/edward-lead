<?php

namespace Tests\unit\AppBundle\Entity\Room;

use AppBundle\Entity\Room\Schedule;
use PHPUnit\Framework\TestCase;

class ScheduleTest extends TestCase
{
    public function testAddDayOfWeek_withEmptyDaysOfWeek()
    {
        $schedule = new Schedule();
        $schedule->addDayOfWeek(3);
        $schedule->addDayOfWeek(2);

        $daysOfWeek = $schedule->getDaysOfWeek();

        $this->assertArraySubset([2,3], $daysOfWeek);
    }

    public function testConstructor()
    {
        $schedule = new Schedule([5,3,1,4,2]);

        $daysOfWeek = $schedule->getDaysOfWeek();

        $this->assertArraySubset([1,2,3,4,5], $daysOfWeek);
    }
}
