<?php

namespace Tests\unit\AppBundle\Notifications;

use AppBundle\Entity\ClientAccount;
use AppBundle\Entity\Lead;
use AppBundle\Entity\Member;
use AppBundle\Entity\Message;
use AppBundle\Entity\Room;
use AppBundle\Entity\Thread;
use AppBundle\Entity\Trade;
use AppBundle\Entity\User;
use AppBundle\Notifications\EmailNotificationContainer;
use AppBundle\Notifications\WebPushNotificationContainer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use NotificationBundle\Client\Client;
use PHPUnit\Framework\TestCase;

class WebPushNotificationContainerTest extends TestCase
{
    const MEMBERS_COUNT = 10;

    /**
     * @var EmailNotificationContainer
     */
    private $service;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */

    private $clientMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $entityManagerMock;

    public function setUp()
    {
        $this->clientMock = $this->createMock(Client::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $this->service = new WebPushNotificationContainer($this->clientMock, $this->entityManagerMock);
    }

    public function testAccountBalanceApproachingZero()
    {
        $this->clientMock->expects($this->once())->method('send');
        $this->service->accountBalanceApproachingZero($this->getAccount());
    }

    public function testMessageSupportReply()
    {
        $this->clientMock->expects($this->once())->method('send');
        $this->service->messageSupportReply($this->getMessage());
    }

    public function testLeadNewPlaced()
    {
        $this->clientMock->expects($this->once())->method('send');

        $this->entityManagerMock->expects($this->any())
            ->method('getByRooms')
            ->willReturn([$this->getMembers()]);


        $this->service->leadNewPlaced($this->getLead());
    }

    public function testLeadExpectTooLong()
    {
        $this->clientMock->expects($this->exactly(self::MEMBERS_COUNT))->method('send');

        $this->entityManagerMock->expects($this->any())
            ->method('findBy')
            ->willReturn([$this->getMembers()]);

        $this->service->leadExpectTooLong($this->getLead());
    }

    public function testLeadInWorkTooLong()
    {
        $this->clientMock->expects($this->once())->method('send');
        $this->service->leadInWorkTooLong($this->getLead());
    }

    //

    private function getAccount()
    {
        $user = new User();
        $user
            ->setName('Company 1')
            ->setEmail('company1@xoptov.ru')
            ->setPhone('79000000001')
            ->setPlainPassword(123456);

        $account = new ClientAccount();
        $account
            ->setUser($user);

        return $account;
    }

    private function getLead()
    {
        $user = new User();
        $user
            ->setName('Company 1')
            ->setEmail('company1@xoptov.ru')
            ->setPhone('79000000001')
            ->setPlainPassword(123456);

        $room = new Room();
        $room->setName(' комната')
            ->setSphere(' сфера');

        $trade = new Trade();
        $trade->setBuyer($user);

        $lead = new Lead();
        $lead
            ->setPhone('79000000003')
            ->setStatus(Lead::STATUS_EXPECT)
            ->setPrice(10000)
            ->setUser($user)
            ->setTrade($trade)
            ->setRoom($room);

        return $lead;
    }

    private function getMessage()
    {
        $thread = new Thread();

        $user = new User();
        $user
            ->setName('Company 1')
            ->setEmail('company1@xoptov.ru')
            ->setPhone('79000000001')
            ->setPlainPassword(123456);

        $object = new Message();
        $object->setThread($thread);
        $object->setSender($user);

        return $object;
    }

    private function getMembers()
    {

        $members = new ArrayCollection();

        for ($i = 0; $i < self::MEMBERS_COUNT; $i++) {

            $user = new User();
            $user
                ->setEmail("company{$i}@xoptov.ru")
                ->setRoles([User::ROLE_WEBMASTER]);

            $member = new Member();
            $member->setUser($user);

            $members->add($member);
        }

        return $members;

    }

}
