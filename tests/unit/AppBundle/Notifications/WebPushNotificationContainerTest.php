<?php

namespace Tests\unit\AppBundle\Notifications;

use AppBundle\Notifications\EmailNotificationContainer;
use AppBundle\Notifications\WebPushNotificationContainer;
use AppBundle\Repository\MemberRepository;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use NotificationBundle\Channels\WebPushChannel;
use NotificationBundle\Client\Client;

class WebPushNotificationContainerTest extends BaseNotificationContainerTestCase
{
    /**
     * @var EmailNotificationContainer
     */
    private $service;

    /**
     * @var Client
     */
    private $clientMock;

    /**
     * @var EntityManagerInterface
     */
    private $entityManagerMock;

    /**
     * @var MemberRepository
     */
    private $repositoryMock;

    public function setUp()
    {
        $this->clientMock = $this->createMock(WebPushChannel::class);
        $this->entityManagerMock = $this->createMock(EntityManager::class);
        $this->repositoryMock = $this->createMock(MemberRepository::class);

        $this->entityManagerMock->expects($this->any())
            ->method('getRepository')
            ->willReturn($this->repositoryMock);

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
        $this->clientMock->expects($this->exactly(self::MEMBERS_COUNT))->method('send');

        $this->repositoryMock->expects($this->any())
            ->method('findBy')
            ->willReturn($this->getMembers());


        $this->service->leadNewPlaced($this->getLead());
    }

    public function testLeadExpectTooLong()
    {
        $this->clientMock->expects($this->exactly(self::MEMBERS_COUNT))->method('send');

        $this->repositoryMock->expects($this->any())
            ->method('findBy')
            ->willReturn($this->getMembers());

        $this->service->leadExpectTooLong($this->getLead());
    }

    public function testLeadInWorkTooLong()
    {
        $this->clientMock->expects($this->once())->method('send');
        $this->service->leadInWorkTooLong($this->getLead());
    }
}
