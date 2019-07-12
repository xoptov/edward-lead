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
}