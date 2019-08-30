<?php

namespace unit\AppBundle\Service;

use AppBundle\Entity\Fee;
use AppBundle\Entity\Lead;
use AppBundle\Entity\Room;
use AppBundle\Entity\Trade;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use AppBundle\Service\FeesManager;

class FeesManagerTest extends TestCase
{
    public function testCalculateTradeFee()
    {
        $amount = 100000;
        $interest = 5.0;

        $entityManager = $this->createMock(EntityManager::class);

        /** @var EntityManager $entityManager */
        $feesManager = new FeesManager($entityManager, 0, 0);

        $feeAmount = $feesManager->calculateTradeFee($amount, $interest);

        $this->assertEquals(5000, $feeAmount);
    }

    public function testCreateForTrade_inExchange()
    {
        $buyer = new User();
        $lead = new Lead();

        $trade = new Trade();
        $trade
            ->setLead($lead)
            ->setBuyer($buyer)
            ->setAmount(10000);

        $entityManager = $this->createMock(EntityManager::class);
        $entityManager
            ->expects($this->once())
            ->method('persist');

        /** @var EntityManager $entityManager */
        $feesManager = new FeesManager($entityManager, 10.0, 0);

        $fees = $feesManager->createForTrade($trade, false);

        $this->assertCount(1, $fees);

        $fee = $fees[0];

        $this->assertInstanceOf(Fee::class, $fee);

        $this->assertEquals($buyer, $fee->getPayer());
        $this->assertEquals(1000, $fee->getAmount());
        $this->assertEquals($trade, $fee->getOperation());
    }

    public function testCreateForTrade_inRoomWithoutCustomInterest()
    {
        $buyer = new User();
        $room = new Room();

        $lead = new Lead();
        $lead->setRoom($room);

        $trade = new Trade();
        $trade
            ->setLead($lead)
            ->setBuyer($buyer)
            ->setAmount(10000);

        $entityManager = $this->createMock(EntityManager::class);
        $entityManager
            ->expects($this->once())
            ->method('persist');

        /** @var EntityManager $entityManager */
        $feesManager = new FeesManager($entityManager, 10.0, 0);

        $fees = $feesManager->createForTrade($trade, false);

        $this->assertCount(1, $fees);

        $fee = $fees[0];

        $this->assertInstanceOf(Fee::class, $fee);

        $this->assertEquals($buyer, $fee->getPayer());
        $this->assertEquals(1000, $fee->getAmount());
        $this->assertEquals($trade, $fee->getOperation());
    }

    public function testCreateForTrade_inRoomWithCustomInterest()
    {
        $buyer = new User();
        $room = new Room();
        $room->setBuyerFee(15.0);

        $lead = new Lead();
        $lead->setRoom($room);

        $trade = new Trade();
        $trade
            ->setLead($lead)
            ->setBuyer($buyer)
            ->setAmount(10000);

        $entityManager = $this->createMock(EntityManager::class);
        $entityManager
            ->expects($this->once())
            ->method('persist');

        /** @var EntityManager $entityManager */
        $feesManager = new FeesManager($entityManager, 10.0, 0);

        $fees = $feesManager->createForTrade($trade, false);

        $this->assertCount(1, $fees);

        $fee = $fees[0];

        $this->assertInstanceOf(Fee::class, $fee);

        $this->assertEquals($buyer, $fee->getPayer());
        $this->assertEquals(1500, $fee->getAmount());
        $this->assertEquals($trade, $fee->getOperation());
    }
}