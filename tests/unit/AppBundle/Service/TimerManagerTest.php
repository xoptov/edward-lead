<?php

namespace Tests\unit\AppBundle\Service;

use PHPUnit\Framework\TestCase;
use AppBundle\Service\TimerManager;
use AppBundle\Entity\Room\Schedule;
use AppBundle\Entity\Room\Schedule\WorkTime;

class TimerManagerTest extends TestCase
{
    public function testCalculateOffsetInHours_withNormalSchedule()
    {
        $schedule = new Schedule();
        $schedule->setWorkDays(array_sum([1,4,16]));

        $workTime = new WorkTime();
        $workTime->setStartAt(date_create_from_format('H:i', '10:00'));
        $workTime->setEndAt(date_create_from_format('H:i', '13:00'));

        $schedule->setWorkTime($workTime);

        $timerManager = new TimerManager();

        $timerManager->calculateOffsetInHours($schedule, new \DateTimeZone('Asia/Vladivostok'), 8);

        return;
    }

    public function testCalculateOffsetInHours_withNightSchedule()
    {
        $schedule = new Schedule();
        $schedule->setWorkDays(array_sum([1,2,4,8,16]));

        $workTime = new WorkTime();
        $workTime->setStartAt(date_create_from_format('H:i', '20:00'));
        $workTime->setEndAt(date_create_from_format('H:i', '06:00'));

        $schedule->setWorkTime($workTime);

        $timerManager = new TimerManager();

        $timerManager->calculateOffsetInHours($schedule, new \DateTimeZone('Asia/Vladivostok'), 8);

        return;
    }
}