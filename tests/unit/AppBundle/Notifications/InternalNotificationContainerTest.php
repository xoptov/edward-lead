<?php

namespace Tests\unit\AppBundle\Notifications;

use AppBundle\Notifications\EmailNotificationContainer;
use AppBundle\Notifications\InternalNotificationContainer;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use NotificationBundle\Client\InternalClient;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class InternalNotificationContainerTest extends BaseNotificationContainerTestCase
{

    /**
     * @var EmailNotificationContainer
     */
    private $service;

    /**
     * @var UserRepository
     */
    private $repositoryMock;

    /**
     * @var InternalClient
     */
    private $clientMock;

    public function setUp()
    {
        $this->clientMock = $this->createMock(InternalClient::class);

        /** @var EntityManagerInterface $entityManagerMock */
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);

        $this->repositoryMock = $this->createMock(UserRepository::class);

        $entityManagerMock->expects($this->any())
            ->method('getRepository')
            ->willReturn($this->repositoryMock);

        $urlGenerator->expects($this->any())
            ->method('generate')
            ->willReturn('/room/1');

        $this->service = new InternalNotificationContainer($this->clientMock, $urlGenerator, $entityManagerMock);
    }

    public function testAccountBalanceLowerThenMinimal()
    {
        $this->clientMock->expects($this->once())->method('send');
        $this->service->accountBalanceApproachingZero($this->getAccount());
    }

    public function testNewRoomCreated()
    {
        $this->clientMock->expects($this->once())->method('send');
        $this->service->newRoomCreated($this->getRoom());
    }

    public function testSomeOneJoinedToYou()
    {
        $this->clientMock->expects($this->once())->method('send');
        $this->repositoryMock->expects($this->any())
            ->method('findBy')
            ->willReturn([$this->getMember(10)]);

        $this->service->someOneJoinedToYou($this->getMember());
    }

    public function testTradeAccepted()
    {
        $this->clientMock->expects($this->once())->method('send');
        $this->service->tradeAccepted($this->getTrade());
    }

    public function testMessageAboutLead()
    {
        $this->clientMock->expects($this->once())->method('send');
        $this->service->messageAboutLead($this->getMessage());
    }

    public function testYouRemovedFromRoom()
    {
        $this->clientMock->expects($this->once())->method('send');
        $this->service->youRemovedFromRoom($this->getMember());
    }

    public function testAccountBalanceApproachingZero()
    {
        $this->clientMock->expects($this->once())->method('send');
        $this->service->accountBalanceApproachingZero($this->getAccount());
    }

    public function testLeadNewPlaced()
    {
        $this->clientMock->expects($this->exactly(self::MEMBERS_COUNT))->method('send');

        $this->repositoryMock->expects($this->any())
            ->method('findBy')
            ->willReturn($this->getMembers());

        $this->service->leadNewPlaced($this->getLead());
    }

    public function testTradeRejected()
    {
        $this->clientMock->expects($this->once())->method('send');
        $this->service->tradeRejected($this->getTrade());
    }

    public function testRoomDeactivated()
    {
        $this->clientMock->expects($this->once())->method('send');
        $this->service->roomDeactivated($this->getRoom());
    }

    public function testInvoiceProcessed()
    {
        $this->clientMock->expects($this->once())->method('send');
        $this->service->invoiceProcessed($this->getInvoice());
    }

    public function testYouJoinedToRoom()
    {
        $this->clientMock->expects($this->once())->method('send');
        $this->service->youJoinedToRoom($this->getMember());
    }

    public function testWithdrawUser()
    {
        $this->clientMock->expects($this->once())->method('send');
        $this->service->withdrawUser($this->getWithdraw());
    }
}
