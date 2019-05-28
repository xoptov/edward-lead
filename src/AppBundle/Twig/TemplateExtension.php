<?php

namespace AppBundle\Twig;


class TemplateExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter("hidden_phone", [$this, "hiddenPhone"]),
            new \Twig_SimpleFilter("date_format", [$this, "dateFormat"]),
            new \Twig_SimpleFilter("money_format", [$this, "moneyFormat"])
        ];
    }

    /**
     * @param $phone
     * @return string
     */
    public function hiddenPhone($phone): string
    {
        if (substr($phone, 0, 1) == "+") {
            return sprintf(
                "%s %s %s ** **",
                substr($phone, 0, 2),
                substr($phone, 2, 3),
                substr($phone, 5, 3)
            );
        }
        return sprintf(
            "%s %s %s ** **",
            substr($phone, 0, 1),
            substr($phone, 1, 3),
            substr($phone, 4, 3)
        );
    }

    /**
     * @param \DateTime $dateTime
     * @return string
     */
    public function dateFormat(\DateTime $dateTime): string
    {
        $day = date_format($dateTime, 'j');
        $month = date_format($dateTime, 'n');
        $year = date_format($dateTime, 'Y');

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
     * @param int $money
     * @return string
     */
    public function moneyFormat(int $money): string
    {
        $result = intdiv($money, 100) . ' руб.';
        if ($end = $money % 100) {
            $result .= ' ' . $money % 100 . ' коп.';
        }
        return $result;
    }
}