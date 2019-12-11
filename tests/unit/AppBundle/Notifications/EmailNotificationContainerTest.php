<?php

namespace Tests\unit\AppBundle\Notifications;

use AppBundle\Entity\Invoice;
use AppBundle\Entity\Lead;
use AppBundle\Entity\Room;
use AppBundle\Entity\Trade;
use AppBundle\Entity\User;
use AppBundle\Notifications\EmailNotificationContainer;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use NotificationBundle\Channels\EmailChannel;
use NotificationBundle\Client\Client;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AccountBalanceApproachingZeroNotificationTest extends BaseNotificationContainerTestCase
{
    /**
     * @var EmailNotificationContainer
     */
    private $service;

    public function setUp()
    {

        /** @var EmailChannel $emailClientMock */
        $emailClientMock = $this->createMock(EmailChannel::class);

        /** @var EntityManagerInterface $entityManagerMock */
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);

        /** @var UserRepository $repositoryMock */
        $repositoryMock = $this->createMock(UserRepository::class);

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);

        $emailClientMock->expects($this->once())->method('send');

        $repositoryMock->expects($this->any())
            ->method('getByRole')
            ->willReturn([$this->getUser()]);

        $entityManagerMock->expects($this->any())
            ->method('getRepository')
            ->willReturn($repositoryMock);

        $urlGenerator->expects($this->any())
            ->method('generate')
            ->willReturn('/room/1');

        $this->service = new EmailNotificationContainer($emailClientMock, $urlGenerator, $entityManagerMock);
    }

    public function testAccountBalanceApproachingZero()
    {
        $this->service->accountBalanceApproachingZero($this->getAccount());
    }

    public function testAccountBalanceLowerThenMinimal()
    {
        $this->service->accountBalanceLowerThenMinimal($this->getAccount());
    }

    public function testInvoiceProcessed()
    {
        $user = new User();
        $user
            ->setName('Company 1')
            ->setEmail('company1@xoptov.ru')
            ->setPhone('79000000001')
            ->setPlainPassword(123456);

        $invoice = new Invoice();
        $invoice
            ->setUser($user)
            ->setAmount(100)
            ->setDescription('Some Description');

        $this->service->invoiceProcessed($invoice);

    }

    public function testLeadNewPlaced()
    {
        $this->service->leadNewPlaced($this->getLead());
    }

    public function testNoVisitTooLong()
    {
        $this->service->noVisitTooLong($this->getMember());
    }

    public function testMessageCreated()
    {
        $this->service->messageCreated($this->getMessage());
    }

    public function testMessageSupportReply()
    {
        $this->service->messageSupportReply($this->getMessage());
    }

    public function testNewUserRegistered()
    {
        $this->service->newUserRegistered($this->getUser());
    }

    public function testTradeProceeding()
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

        $lead = new Lead();
        $lead
            ->setPhone('79000000003')
            ->setStatus(Lead::STATUS_EXPECT)
            ->setPrice(10000)
            ->setUser($user)
            ->setRoom($room);

        $object = new Trade();
        $object->setLead($lead);

        $this->service->tradeProceeding($object);
    }

    public function testUserApiTokenChanged()
    {
        $this->service->userApiTokenChanged($this->getUser());
    }

    public function testUserPasswordChanged()
    {
        $this->service->userPasswordChanged($this->getUser());
    }

    public function testUserResetTokenUpdated()
    {
        $this->service->userResetTokenUpdated($this->getUser());
    }

    public function testWithdrawAccepted()
    {
        $this->service->withdrawAccepted($this->getWithdraw());
    }

    public function testWithdrawAdmin()
    {
        $this->service->withdrawAdmin($this->getWithdraw());
    }

    public function testWithdrawRejected()
    {
        $this->service->withdrawRejected($this->getWithdraw());
    }

    public function testWithdrawUser()
    {
        $this->service->withdrawUser($this->getWithdraw());
    }
}
