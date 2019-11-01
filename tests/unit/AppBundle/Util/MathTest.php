<?php

namespace Tests\unit\AppBundle\Util;

use AppBundle\Util\Math;
use PHPUnit\Framework\TestCase;

class MathTest extends TestCase
{
    public function testCalculateByInterest()
    {
        $amount = 100000;
        $interest = 5.0;

        $feeAmount = Math::calculateByInterest($amount, $interest);

        $this->assertEquals(5000, $feeAmount);
    }
}