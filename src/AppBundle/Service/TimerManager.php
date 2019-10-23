<?php

namespace AppBundle\Service;

use DateTime;
use DateTimeZone;
use AppBundle\Entity\Room\Schedule;

class TimerManager
{
    /**
     * @param DateTimeZone $timezone
     *
     * @return DateTime
     */
    public function now(DateTimeZone $timezone): DateTime
    {
        return new DateTime(null, $timezone);
    }

    /**
     * @param Schedule     $schedule
     * @param DateTimeZone $timezone
     * @param int          $execHours
     *
     * @return int
     *
     * @throws \Exception
     */
    public function calculateOffsetInHours(
        Schedule $schedule,
        DateTimeZone $timezone,
        int $execHours
    ): int {

        $scheduleMatrix = $schedule->getMatrix();
        $remoteDateTime = $this->now($timezone);

        $startDayOfWeek = (int)$remoteDateTime->format('N');
        $startHour = (int)$remoteDateTime->format('H');

        if ($scheduleMatrix[$startDayOfWeek][$startHour]) {
            $offsetHours = -1;
        } else {
            $offsetHours = 0;
        }

        for ($filledHours = 0; $filledHours < $execHours; $offsetHours++) {
            for ($dow = $startDayOfWeek; $dow <= 7 && $filledHours < $execHours; $dow++) {
                for ($hour = $startHour; $hour < 24 && $filledHours < $execHours; $hour++, $offsetHours++) {
                    if ($scheduleMatrix[$dow][$hour]) {
                        $filledHours++;
                    }
                }
            }
            $startDayOfWeek = 1;
            $startHour = 0;
        }

        return $offsetHours;
    }
}