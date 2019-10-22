<?php

namespace Tests\unit\AppBundle\Form\DataTransformer;

use AppBundle\Form\DataTransformer\BitMaskToArrayTransformer;
use PHPUnit\Framework\TestCase;

class BitMaskToArrayTransformerTest extends TestCase
{
    public function testTransform_withNull()
    {
        $transformer = new BitMaskToArrayTransformer(1);

        $result = $transformer->transform(null);

        $this->assertEmpty($result);
    }

    public function testTransform_withPartialFills()
    {
        $values = 15;

        $transformer = new BitMaskToArrayTransformer(16);

        $result = $transformer->transform($values);

        $this->assertArraySubset([1,2,4,8], $result);
    }

    public function testTransform_withFullFills()
    {
        $values = 31;

        $transformer = new BitMaskToArrayTransformer(16);

        $result = $transformer->transform($values);

        $this->assertArraySubset([1,2,4,8,16], $result);
    }

    public function testReverseTransform_withNull()
    {
        $transformer = new BitMaskToArrayTransformer(1);

        $result = $transformer->reverseTransform(null);

        $this->assertNull($result);
    }

    public function testReverseTransform_withPartialFills()
    {
        $values = [1,2,4,8];

        $transformer = new BitMaskToArrayTransformer(16);

        $result = $transformer->reverseTransform($values);

        $this->assertEquals(15, $result);
    }

    public function testReverseTransform_withFullFills()
    {
        $values = [1,2,4,8,16];

        $transformer = new BitMaskToArrayTransformer(16);

        $result = $transformer->reverseTransform($values);

        $this->assertEquals(31, $result);
    }
}