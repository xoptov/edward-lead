<?php

namespace Tests\unit\AppBundle\Service;

use AppBundle\Entity\Lead;
use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use AppBundle\Entity\Trade;
use AppBundle\Service\LeadManager;
use AppBundle\Service\TimerManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class LeadManagerTest extends TestCase
{
    public function testIsCanShowPhone_onExchange_forLeadOwner()
    {
        $owner = new User();
        $lead = new Lead();

        $lead->setUser($owner);

        $leadManager = $this->createLeadManager();

        $this->assertTrue($leadManager->isCanShowPhone($lead, $owner));
    }

    public function testIsCanShowPhone_onExchange_forNoBuyer_and_leadStatusExpect()
    {
        $user = new User();
        $lead = new Lead();
        $lead->setStatus(Lead::STATUS_EXPECT);

        $leadManager = $this->createLeadManager();

        $this->assertFalse($leadManager->isCanShowPhone($lead, $user));
    }

    public function testIsCanShowPhone_onExchange_forNoBuyer_and_leadStatusArchive()
    {
        $user = new User();
        $lead = new Lead();

        $lead->setStatus(Lead::STATUS_ARCHIVE);

        $leadManager = $this->createLeadManager();

        $this->assertFalse($leadManager->isCanShowPhone($lead, $user));
    }

    public function testIsCanShowPhone_onExchange_forNoBuyer_andLeadStatusInWork()
    {
        $user = new User();
        $lead = new Lead();
        $lead->setStatus(Lead::STATUS_IN_WORK);

        $leadManager = $this->createLeadManager();

        $this->assertFalse($leadManager->isCanShowPhone($lead, $user));
    }

    public function testIsCanShowPhone_onExchange_forBuyer_andLeadStatusInWork_andTradeStatusNew()
    {
        $buyer = new User();

        $lead = new Lead();
        $lead->setStatus(Lead::STATUS_IN_WORK);

        $trade = new Trade();
        $trade
            ->setLead($lead)
            ->setBuyer($buyer);

        $lead->setTrade($trade);

        $leadManager = $this->createLeadManager();

        $this->assertFalse($leadManager->isCanShowPhone($lead, $buyer));
    }

    public function testIsCanShowPhone_onExchange_forBuyer_andLeadStatusArbitration_andTradeStatusProceeding()
    {
        $buyer = new User();

        $lead = new Lead();
        $lead->setStatus(Lead::STATUS_ARBITRATION);

        $trade = new Trade();
        $trade
            ->setLead($lead)
            ->setBuyer($buyer)
            ->setStatus(Trade::STATUS_PROCEEDING);

        $lead->setTrade($trade);

        $leadManager = $this->createLeadManager();

        $this->assertFalse($leadManager->isCanShowPhone($lead, $buyer));
    }

    public function testIsCanShowPhone_onExchange_forBuyer_andLeadStatusTarget_andTradeStatusAccepted()
    {
        $buyer = new User();

        $lead = new Lead();
        $lead->setStatus(Lead::STATUS_TARGET);

        $trade = new Trade();
        $trade
            ->setLead($lead)
            ->setBuyer($buyer)
            ->setStatus(Trade::STATUS_ACCEPTED);

        $lead->setTrade($trade);

        $leadManager = $this->createLeadManager();

        $this->assertTrue($leadManager->isCanShowPhone($lead, $buyer));
    }

    public function testIsCanShowPhone_onExchange_forBuyer_andLeadStatusNoTarget_andTradeStatusRejected()
    {
        $buyer = new User();

        $lead = new Lead();
        $lead->setStatus(Lead::STATUS_NOT_TARGET);

        $trade = new Trade();
        $trade
            ->setLead($lead)
            ->setBuyer($buyer)
            ->setStatus(Trade::STATUS_REJECTED);

        $lead->setTrade($trade);

        $leadManager = $this->createLeadManager();

        $this->assertFalse($leadManager->isCanShowPhone($lead, $buyer));
    }

    public function testIsCanShowPhone_onRoomWithoutWarranty_forOwner()
    {
        $owner = new User();

        $room = new Room();
        $room->setPlatformWarranty(false);

        $lead = new Lead();
        $lead
            ->setUser($owner)
            ->setRoom($room);

        $leadManager = $this->createLeadManager();
        $this->assertTrue($leadManager->isCanShowPhone($lead, $owner));
    }

    public function testIsCanShowPhone_onRoomWithoutWarranty_forNoBuyer_andLeadStatusExpect()
    {
        $user = new User();

        $room = new Room();
        $room->setPlatformWarranty(false);

        $lead = new Lead();
        $lead->setRoom($room);

        $leadManager = $this->createLeadManager();

        $this->assertFalse($leadManager->isCanShowPhone($lead, $user));
    }

    public function testIsCanShowPhone_onRoomWithoutWarranty_forBuyer_andLeadStatusInWork_andTradeStatusNew()
    {
        $buyer = new User();

        $room = new Room();
        $room->setPlatformWarranty(false);

        $lead = new Lead();
        $lead
            ->setRoom($room)
            ->setStatus(Lead::STATUS_IN_WORK);

        $trade = new Trade();
        $trade
            ->setBuyer($buyer)
            ->setLead($lead);

        $lead->setTrade($trade);

        $leadManager = $this->createLeadManager();

        $this->assertTrue($leadManager->isCanShowPhone($lead, $buyer));
    }

    public function testIsCanShowPhone_onRoomWithoutWarranty_forBuyer_andLeadStatusTarget_andTradeStatusAccepted()
    {
        $buyer = new User();

        $room = new Room();
        $room->setPlatformWarranty(false);

        $lead = new Lead();
        $lead
            ->setRoom($room)
            ->setStatus(Lead::STATUS_TARGET);

        $trade = new Trade();
        $trade
            ->setBuyer($buyer)
            ->setLead($lead)
            ->setStatus(Trade::STATUS_ACCEPTED);

        $lead->setTrade($trade);

        $leadManager = $this->createLeadManager();

        $this->assertTrue($leadManager->isCanShowPhone($lead, $buyer));
    }

    public function testIsCanShowPhone_onRoomWithoutWarranty_forBuyer_andLeadStatusNoTarget_andTradeStatusRejected()
    {
        $buyer = new User();

        $room = new Room();
        $room->setPlatformWarranty(false);

        $lead = new Lead();
        $lead
            ->setRoom($room)
            ->setStatus(Lead::STATUS_NOT_TARGET);

        $trade = new Trade();
        $trade
            ->setBuyer($buyer)
            ->setLead($lead)
            ->setStatus(Trade::STATUS_REJECTED);

        $leadManager = $this->createLeadManager();

        $this->assertFalse($leadManager->isCanShowPhone($lead, $buyer));
    }

    private function createLeadManager()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $timerManager = $this->createMock(TimerManager::class);

        $logger = $this->createMock(LoggerInterface::class);

        /**
         * @var EntityManagerInterface $entityManager
         * @var TimerManager           $timerManager
         * @var LoggerInterface        $logger
         */

        return new LeadManager($entityManager, $timerManager, $logger, 10000, 1000, 10);
    }
}