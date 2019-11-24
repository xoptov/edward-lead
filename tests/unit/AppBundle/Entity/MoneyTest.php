<?php

namespace Tests\unit\AppBundle\Entity;

use AppBundle\Entity\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function testConstructor_withPositiveValue()
    {
        $money = new Money(199);

        $this->assertEquals(1, $money->getRubles());
        $this->assertEquals(99, $money->getPennies());
        $this->assertFalse($money->isNegative());
    }

    public function testConstructor_withNegativeValue()
    {
        $money = new Money(-199);

        $this->assertEquals(1, $money->getRubles());
        $this->assertEquals(99, $money->getPennies());
        $this->assertTrue($money->isNegative());
    }

    public function testAdd_withPositiveMoney()
    {
        $operand1 = new Money(110);
        $operand2 = new Money(190);

        $result = $operand1->add($operand2);

        $this->assertEquals(3, $result->getRubles());
        $this->assertEquals(0, $result->getPennies());
        $this->assertFalse($result->isNegative());
    }

    public function testAdd_withNegativeMoney()
    {
        $operand1 = new Money(110);
        $operand2 = new Money(-190);

        $result = $operand1->add($operand2);

        $this->assertEquals(0, $result->getRubles());
        $this->assertEquals(80, $result->getPennies());
        $this->assertTrue($result->isNegative());
    }

    public function testAdd_withPositiveFloatValue()
    {
        $operand1 = new Money(110);
        $operand2 = 1.90;

        $result = $operand1->add($operand2);

        $this->assertEquals(3, $result->getRubles());
        $this->assertEquals(0, $result->getPennies());
        $this->assertFalse($result->isNegative());
    }

    public function testAdd_withNegativeFloatValue()
    {
        $operand1 = new Money(110);
        $operand2 = -1.90;

        $result = $operand1->add($operand2);

        $this->assertEquals(0, $result->getRubles());
        $this->assertEquals(80, $result->getPennies());
        $this->assertTrue($result->isNegative());
    }

    public function testAdd_withPositiveIntegerValue()
    {
        $operand1 = new Money(110);
        $operand2 = 190;

        $result = $operand1->add($operand2);

        $this->assertEquals(3, $result->getRubles());
        $this->assertEquals(0, $result->getPennies());
        $this->assertFalse($result->isNegative());
    }

    public function testAdd_withNegativeIntegerValue()
    {
        $operand1 = new Money(110);
        $operand2 = -190;

        $result = $operand1->add($operand2);

        $this->assertEquals(0, $result->getRubles());
        $this->assertEquals(80, $result->getPennies());
        $this->assertTrue($result->isNegative());
    }

    public function testSubstract_withPositiveMoney()
    {
        $operand1 = new Money(110);
        $operand2 = new Money(20);

        $result = $operand1->substract($operand2);

        $this->assertEquals(0, $result->getRubles());
        $this->assertEquals(90, $result->getPennies());
        $this->assertFalse($result->isNegative());
    }

    public function testSubstract_withNegativeMoney()
    {
        $operand1 = new Money(110);
        $operand2 = new Money(-20);

        $result = $operand1->substract($operand2);

        $this->assertEquals(1, $result->getRubles());
        $this->assertEquals(30, $result->getPennies());
        $this->assertFalse($result->isNegative());
    }

    public function testSubstract_withPositiveFloatValue()
    {
        $operand1 = new Money(110);
        $operand2 = 0.20;

        $result = $operand1->substract($operand2);

        $this->assertEquals(0, $result->getRubles());
        $this->assertEquals(90, $result->getPennies());
        $this->assertFalse($result->isNegative());
    }

    public function testSubstract_withNegativeFloatValue()
    {
        $operand1 = new Money(110);
        $operand2 = -0.20;

        $result = $operand1->substract($operand2);

        $this->assertEquals(1, $result->getRubles());
        $this->assertEquals(30, $result->getPennies());
        $this->assertFalse($result->isNegative());
    }

    public function testSubstract_withPositiveInteger()
    {
        $operand1 = new Money(110);
        $operand2 = 20;

        $result = $operand1->substract($operand2);

        $this->assertEquals(0, $result->getRubles());
        $this->assertEquals(90, $result->getPennies());
        $this->assertFalse($result->isNegative());
    }

    public function testSubstract_withNegativeInteger()
    {
        $operand1 = new Money(110);
        $operand2 = -20;

        $result = $operand1->substract($operand2);

        $this->assertEquals(1, $result->getRubles());
        $this->assertEquals(30, $result->getPennies());
        $this->assertFalse($result->isNegative());
    }

    public function testMultiple_byPositiveFloatValue()
    {
        $operand1 = new Money(125);
        $operand2 = 2.0;

        $result = $operand1->multiple($operand2);

        $this->assertEquals(2, $result->getRubles());
        $this->assertEquals(50, $result->getPennies());
        $this->assertFalse($result->isNegative());
    }

    public function testMultiple_byNegativeFloatValue()
    {
        $operand1 = new Money(125);
        $operand2 = -2.0;

        $result = $operand1->multiple($operand2);

        $this->assertEquals(2, $result->getRubles());
        $this->assertEquals(50, $result->getPennies());
        $this->assertTrue($result->isNegative());
    }

    public function testDevide_byPositiveMoney()
    {
        $operand1 = new Money(150);
        $operand2 = new Money(200);

        $result = $operand1->divide($operand2);

        $this->assertEquals(0.75, $result);
    }

    public function testDevide_byNegativeMoney()
    {
        $operand1 = new Money(150);
        $operand2 = new Money(-200);

        $result = $operand1->divide($operand2);

        $this->assertEquals(-0.75, $result);
    }

    public function testDivide_byPositiveFloatValue()
    {
        $operand1 = new Money(150);
        $operand2 = 2.0;

        $result = $operand1->divide($operand2);

        $this->assertEquals(0.75, $result);
    }

    public function testDivide_byNegativeFloatValue()
    {
        $operand1 = new Money(150);
        $operand2 = -2.0;

        $result = $operand1->divide($operand2);

        $this->assertEquals(-0.75, $result);
    }

    public function testIsEqual_withEqualMoney()
    {
        $operand1 = new Money(125);
        $operand2 = new Money(125);

        $this->assertTrue($operand1->isEqual($operand2));
    }

    public function testIsEqual_withNotEqualMoney()
    {
        $operand1 = new Money(125);
        $operand2 = new Money(275);

        $this->assertFalse($operand1->isEqual($operand2));
    }

    public function testIsEqual_withEqualFloat()
    {
        $operand1 = new Money(125);
        $operand2 = 1.25;

        $this->assertTrue($operand1->isEqual($operand2));
    }

    public function testIsEqual_withNotEqualFloat()
    {
        $operand1 = new Money(125);
        $operand2 = 2.75;

        $this->assertFalse($operand1->isEqual($operand2));
    }

    public function testIsEqual_withEqualInteger()
    {
        $operand1 = new Money(125);
        $operand2 = 125;

        $this->assertTrue($operand1->isEqual($operand2));
    }

    public function testIsEqual_withNotEqualInteger()
    {
        $operand1 = new Money(125);
        $operand2 = 275;

        $this->assertFalse($operand1->isEqual($operand2));
    }

    public function testIsLess_withLessMoney()
    {
        $operand1 = new Money(125);
        $operand2 = new Money(075);

        $this->assertFalse($operand1->isLess($operand2));
    }

    public function testIsLess_withEqualMoney()
    {
        $operand1 = new Money(125);
        $operand2 = new Money(125);

        $this->assertFalse($operand1->isLess($operand2));
    }

    public function testIsLess_withGreaterMoney()
    {
        $operand1 = new Money(125);
        $operand2 = new Money(275);

        $this->assertTrue($operand1->isLess($operand2));
    }

    public function testLess_withLessFloat()
    {
        $operand1 = new Money(125);
        $operand2 = 0.75;

        $this->assertFalse($operand1->isLess($operand2));
    }

    public function testIsLess_withEqualFloat()
    {
        $operand1 = new Money(125);
        $operand2 = 1.25;

        $this->assertFalse($operand1->isLess($operand2));
    }

    public function testIsLess_withGreaterFloat()
    {
        $operand1 = new Money(125);
        $operand2 = 2.75;

        $this->assertTrue($operand1->isLess($operand2));
    }

    public function testIsLess_withLessInteger()
    {
        $operand1 = new Money(125);
        $operand2 = 75;

        $this->assertFalse($operand1->isLess($operand2));
    }

    public function testIsLess_withEqualInteger()
    {
        $operand1 = new Money(125);
        $operand2 = 125;

        $this->assertFalse($operand1->isLess($operand2));
    }

    public function testIsLess_withGreaterInteger()
    {
        $operand1 = new Money(125);
        $operand2 = 275;

        $this->assertTrue($operand1->isLess($operand2));
    }

    public function testIsGreater_withLessMoney()
    {
        $operand1 = new Money(125);
        $operand2 = new Money(75);

        $this->assertTrue($operand1->isGreater($operand2));
    }

    public function testIsGreater_withEqualMoney()
    {
        $operand1 = new Money(125);
        $operand2 = new Money(125);

        $this->assertFalse($operand1->isGreater($operand2));
    }

    public function testIsGreater_withGreaterMoney()
    {
        $operand1 = new Money(125);
        $operand2 = new Money(275);

        $this->assertFalse($operand1->isGreater($operand2));
    }

    public function testIsGreater_withLessFloat()
    {
        $operand1 = new Money(125);
        $operand2 = 0.75;

        $this->assertTrue($operand1->isGreater($operand2));
    }

    public function testIsGreater_withEqualFlaot()
    {
        $operand1 = new Money(125);
        $operand2 = 1.25;

        $this->assertFalse($operand1->isGreater($operand2));
    }

    public function testIsGreater_withGreaterFloat()
    {
        $operand1 = new Money(125);
        $operand2 = 2.75;

        $this->assertFalse($operand1->isGreater($operand2));
    }

    public function testIsGreater_withLessInteger()
    {
        $operand1 = new Money(125);
        $operand2 = 75;

        $this->assertTrue($operand1->isGreater($operand2));
    }

    public function testIsGreater_withEqualInteger()
    {
        $operand1 = new Money(125);
        $operand2 = 125;

        $this->assertFalse($operand1->isGreater($operand2));
    }

    public function testIsGreater_withGreaterInteger()
    {
        $operand1 = new Money(125);
        $operand2 = 275;

        $this->assertFalse($operand1->isGreater($operand2));
    }

    public function testCompare_withLessMoney()
    {
        $operand1 = new Money(125);
        $operand2 = new Money(75);

        $this->assertEquals(1, $operand1->compare($operand2));
    }

    public function testCompare_withEqualMoney()
    {
        $operand1 = new Money(125);
        $operand2 = new Money(125);

        $this->assertEquals(0, $operand1->compare($operand2));
    }

    public function testCompany_withGreaterMoney()
    {
        $operand1 = new Money(125);
        $operand2 = new Money(275);

        $this->assertEquals(-1, $operand1->compare($operand2));
    }

    public function testCompany_withLessFloat()
    {
        $operand1 = new Money(125);
        $operand2 = 0.75;

        $this->assertEquals(1, $operand1->compare($operand2));
    }

    public function testCompany_withEqualFloat()
    {
        $operand1 = new Money(125);
        $operand2 = 1.25;

        $this->assertEquals(0, $operand1->compare($operand2));
    }

    public function testCompany_withGreaterFloat()
    {
        $operand1 = new Money(125);
        $operand2 = 2.75;

        $this->assertEquals(-1, $operand1->compare($operand2));
    }

    public function testCompany_withLessInteger()
    {
        $operand1 = new Money(125);
        $operand2 = 75;

        $this->assertEquals(1, $operand1->compare($operand2));
    }

    public function testCompany_withEqualInteger()
    {
        $operand1 = new Money(125);
        $operand2 = 125;

        $this->assertEquals(0, $operand1->compare($operand2));
    }

    public function testCompany_withGreaterInteger()
    {
        $operand1 = new Money(125);
        $operand2 = 275;

        $this->assertEquals(-1, $operand1->compare($operand2));
    }
}
