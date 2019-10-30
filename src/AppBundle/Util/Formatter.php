<?php

namespace AppBundle\Util;

abstract class Formatter
{
    /**
     * @param string|null $phone
     *
     * @return string
     */
    public static function hidePhoneNumber(?string $phone = null): string
    {
        if (!$phone) {
            return '';
        }

        if (strlen($phone) === 3) {
            return sprintf('%s * *', substr($phone,0 , 1));
        }

        return sprintf(
            '+%s(%s)-%s-**-**',
            substr($phone, 0, 1),
            substr($phone, 1, 3),
            substr($phone, 4, 3)
        );
    }

    /**
     * @param \DateTime|null $date
     *
     * @return string
     */
    public static function localizeDate(?\DateTime $date = null): string
    {
        if (!$date) {
            return '';
        }

        $day = date_format($date, 'j');
        $month = date_format($date, 'n');
        $year = date_format($date, 'Y');

        switch ($month) {
            case 1:
                $month = "января";
                break;
            case 2:
                $month = "февраля";
                break;
            case 3:
                $month = "марта";
                break;
            case 4:
                $month = "апреля";
                break;
            case 5:
                $month = "мая";
                break;
            case 6:
                $month = "июня";
                break;
            case 7:
                $month = "июля";
                break;
            case 8:
                $month = "августа";
                break;
            case 9:
                $month = "сентябя";
                break;
            case 10:
                $month = "октября";
                break;
            case 11:
                $month = "ноября";
                break;
            case 12:
                $month = "декабря";
                break;
        }

        return sprintf("%s %s %s г.", $day, $month, $year);
    }

    /**
     * @param string|null $phone
     *
     * @return string
     */
    public static function humanizePhone(?string $phone = null): string
    {
        if (!$phone) {
            return '';
        }

        $formattedPhone = sprintf('+%s(%s)%s-%s-%s',
            substr($phone, 0, 1),
            substr($phone, 1, 3),
            substr($phone, 4, 3),
            substr($phone, 7, 2),
            substr($phone, 9, 2)
        );

        return $formattedPhone;
    }

    /**
     * @param int $money
     *
     * @return string
     */
    public static function humanizeMoney(int $money): string
    {
        $result = intdiv($money, 100) . ' руб.';

        if ($end = $money % 100) {
            $result .= ' ' . $money % 100 . ' коп.';
        }

        return $result;
    }

    /**
     * @param int $seconds
     *
     * @return string
     */
    public static function humanDuration(int $seconds): string
    {
        $minutes = (int)floor($seconds / 60);
        $hours = (int)floor($minutes / 60);

        return sprintf('%02d:%02d:%02d', $hours, $minutes % 60, $seconds % 60);
    }

    /**
     * @param int $seconds
     *
     * @return string
     */
    public static function offsetFormatHHMM(int $seconds): string
    {
        $minutes = (int)floor(abs($seconds) / 60);
        $hours = (int)floor($minutes / 60);

        if ($seconds < 0) {
            $sign = '-';
        } elseif ($seconds > 0) {
            $sign = '+';
        } else {
            $sign = '';
        }

        return sprintf('%s%02d:%02d', $sign, $hours, $minutes % 60);
    }

    /**
     * @param \DateTime $now
     * @param \DateTime $target
     *
     * @return int
     */
    public static function intervalInSeconds(\DateTime $now, \DateTime $target): int
    {
        if ($now > $target) {
            return 0;
        }

        $interval = $now->diff($target);

        $seconds = $interval->s;
        $seconds += $interval->i * 60;
        $seconds += $interval->h * 3600;

        if ($interval->days) {
            $seconds += $interval->days * 86400;
        }

        return $seconds;
    }

    /**
     * @param int $seconds
     *
     * @return null|string
     */
    public static function humanTimerRemain(int $seconds): ?string
    {
        if ($seconds < 60) {
            return null;
        }

        $days = intval($seconds / 86400);
        $hours = intval($seconds % 86400 / 3600);
        $minutes = intval($seconds % 86400 % 3600 / 60);

        if ($days) {
            return sprintf('%d д. %d ч. %d м.', $days, $hours, $minutes);
        }

        if ($hours) {
            return sprintf('%d ч. %d м.', $hours, $minutes);
        }

        if ($minutes) {
            return sprintf('%d м.', $minutes);
        }

        return null;
    }
}