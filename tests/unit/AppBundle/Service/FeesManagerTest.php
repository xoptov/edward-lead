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

        $fee = reset($fees);

        $this->assertInstanceOf(Fee::class, $fee);

        $this->assertEquals($buyer, $fee->getPayer());
        $this->assertEquals(1500, $fee->getAmount());
        $this->assertEquals($trade, $fee->getOperation());
    }

    public function testGetCommissionForBuyerInRoom()
    {
        $roomWithCustomFee = new Room();
        $roomWithCustomFee->setBuyerFee(15);

        $entityManager = $this->createMock(EntityManager::class);

        /** @var EntityManager $entityManager */
        $feesManager = new FeesManager($entityManager, 10, 0);

        $interest = $feesManager->getCommissionForBuyerInRoom($roomWithCustomFee);

        $this->assertEquals(15, $interest);

        $roomWithoutCustomFee = new Room();

        $interest = $feesManager->getCommissionForBuyerInRoom($roomWithoutCustomFee);

        $this->assertEquals(10, $interest);
    }

    public function testGetFeeInterestForBuyingLead()
    {
        $entityManager = $this->createMock(EntityManager::class);

        /** @var EntityManager $entityManager */
        $feesManager = new FeesManager($entityManager, 10, 0);

        $lead1 = new Lead();

        $interest =  $feesManager->getCommissionForBuyingLead($lead1);

        $this->assertEquals(10, $interest);

        $room1 = new Room();
        $lead2 = new Lead();
        $lead2->setRoom($room1);

        $interest =  $feesManager->getCommissionForBuyingLead($lead2);

        $this->assertEquals(10, $interest);

        $room2 = new Room();
        $room2->setBuyerFee(15);

        $lead3 = new Lead();
        $lead3->setRoom($room2);

        $interest =  $feesManager->getCommissionForBuyingLead($lead3);

        $this->assertEquals(15, $interest);
    }
}