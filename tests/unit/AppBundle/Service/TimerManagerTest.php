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

        $workTime = $this->createWorkTime('8:00', '18:00');
        $schedule->setWorkTime($workTime);

        $vladivostokTimeZone = new \DateTimeZone('Asia/Vladivostok');

        $timerManager = $this->createTimerManager('2019-10-25 17:10:00', $vladivostokTimeZone);

        $result = $timerManager->calculateOffsetInHours($schedule, $vladivostokTimeZone, 8);

        $this->assertEquals(70, $result);
    }

    public function testCalculateOffsetInHours_withNightSchedule()
    {
        $schedule = new Schedule();
        $schedule->setWorkDays(array_sum([1,2,4,8,16]));

        $workTime = $this->createWorkTime('20:00', '06:00');
        $schedule->setWorkTime($workTime);

        $vladivostokTimeZone = new \DateTimeZone('Asia/Vladivostok');

        $timerManager = $this->createTimerManager('2019-10-25 19:10:00', $vladivostokTimeZone);

        $result = $timerManager->calculateOffsetInHours($schedule, $vladivostokTimeZone, 8);

        $this->assertEquals(57, $result);
    }

    public function testCalculateOffsetInHours_withSmallSchedule()
    {
        $schedule = new Schedule();
        $schedule->setWorkDays(array_sum([1,4,16]));

        $workTime = $this->createWorkTime('10:00', '13:00');
        $schedule->setWorkTime($workTime);

        $vladivostokTimeZone = new \DateTimeZone('Asia/Vladivostok');

        $timerManager = $this->createTimerManager('2019-10-25 12:10:00', $vladivostokTimeZone);

        $result = $timerManager->calculateOffsetInHours($schedule, $vladivostokTimeZone, 4);

        $this->assertEquals(73, $result);
    }

    public function testCalculateOffsetInHours_withCase_1()
    {
        $schedule = new Schedule();
        $schedule->setWorkDays(array_sum([1,2,4,8,16]));

        $workTime = $this->createWorkTime('8:00', '18:00');
        $schedule->setWorkTime($workTime);

        $vladivostokTimeZone = new \DateTimeZone('Asia/Vladivostok');

        $timerManager = $this->createTimerManager('2019-10-25 17:59:00', $vladivostokTimeZone);

        $result = $timerManager->calculateOffsetInHours($schedule, $vladivostokTimeZone, 3);

        $this->assertEquals(65, $result);
    }

    /**
     * @param string $startAt
     * @param string $endAt
     *
     * @return WorkTime
     */
    private function createWorkTime(string $startAt, string $endAt): WorkTime
    {
        $workTime = new WorkTime();
        $workTime->setStartAt(date_create_from_format('H:i', $startAt));
        $workTime->setEndAt(date_create_from_format('H:i', $endAt));

        return $workTime;
    }

    /**
     * @param string        $time
     * @param \DateTimeZone $timeZone
     *
     * @return TimerManager
     *
     * @throws \ReflectionException
     */
    private function createTimerManager(string $time, \DateTimeZone $timeZone): TimerManager
    {
        $timerManager = $this->createPartialMock(TimerManager::class, ['createDateTime']);
        $timerManager
            ->method('createDateTime')
            ->willReturn(new \DateTime($time, $timeZone));

        /** @var $timerManager TimerManager */
        return $timerManager;
    }
}