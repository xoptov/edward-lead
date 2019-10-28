<?php

namespace Tests\unit\AppBundle\Util;

use AppBundle\Util\Formatter;
use PHPUnit\Framework\TestCase;

class FormatterTest extends TestCase
{
    public function testIntervalInSeconds()
    {
        $now = new \DateTime('2019-10-28');
        $target = new \DateTime('2019-10-31');

        $seconds = Formatter::intervalInSeconds($now, $target);

        $this->assertEquals(259200, $seconds);
    }
}