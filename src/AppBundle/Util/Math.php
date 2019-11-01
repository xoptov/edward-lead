<?php

namespace AppBundle\Util;

abstract class Math
{
    /**
     * @param int   $amount
     * @param float $interest
     *
     * @return int
     */
    public static function calculateByInterest(
        int $amount,
        float $interest
    ): int {
        return floor($amount * $interest / 100);
    }
}