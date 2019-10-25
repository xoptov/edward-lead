<?php

namespace AppBundle\Service;

use DateTime;
use DateTimeZone;
use AppBundle\Entity\Lead\Timer;
use AppBundle\Entity\Room\Schedule;

class TimerManager
{
    /**
     * @param string|null       $time
     * @param DateTimeZone|null $timezone
     *
     * @return DateTime
     */
    public function createDateTime(
        ?string $time = null,
        DateTimeZone $timezone = null
    ): DateTime {
        return new DateTime($time, $timezone);
    }

    /**
     * @param Schedule     $schedule
     * @param DateTimeZone $timezone
     * @param int          $execHours
     *
     * @return int
     */
    public function calculateOffsetInHours(
        Schedule $schedule,
        DateTimeZone $timezone,
        int $execHours
    ): int {

        $scheduleMatrix = $schedule->getMatrix();
        $remoteDateTime = $this->createDateTime(null, $timezone);

        $startDOW = (int)$remoteDateTime->format('N');
        $startHour = (int)$remoteDateTime->format('H');

        $matched = 0;
        $offset = 0;

        for (;$matched < $execHours; $startDOW = 1, $startHour = 0) {
            for ($dow = $startDOW; $dow <= 7 && $matched < $execHours; $dow++, $startHour = 0) {
                for ($hour = $startHour; $hour < 24 && $matched < $execHours; $hour++, $offset++) {
                    if ($scheduleMatrix[$dow][$hour]) {
                        $matched++;
                    }
                }
            }
        }

        return $offset;
    }

    /**
     * @param int $offsetHours
     *
     * @return Timer
     */
    public function createTimer(int $offsetHours): Timer
    {
        $timer = new Timer();

        $endedAt = $this->createDateTime('+'.$offsetHours.' hours');

        $timer->setEndAt($endedAt);

        return $timer;
    }
}