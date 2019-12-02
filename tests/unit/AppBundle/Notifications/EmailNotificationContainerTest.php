<?php

namespace Tests\unit\AppBundle\Notifications;

use AppBundle\Entity\ClientAccount;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\Lead;
use AppBundle\Entity\Member;
use AppBundle\Entity\Message;
use AppBundle\Entity\Room;
use AppBundle\Entity\Thread;
use AppBundle\Entity\Trade;
use AppBundle\Entity\User;
use AppBundle\Entity\Withdraw;
use AppBundle\Notifications\EmailNotificationContainer;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use NotificationBundle\Client\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AccountBalanceApproachingZeroNotificationTest extends TestCase
{
    /**
     * @var EmailNotificationContainer
     */
    private $service;

    public function setUp()
    {
        $emailClientMock = $this->createMock(Client::class);
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $repositoryMock = $this->createMock(UserRepository::class);
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);

        $emailClientMock->expects($this->once())->method('send');

        $repositoryMock->expects($this->any())
            ->method('getByRole')
            ->willReturn([$this->getUser()]);

        $entityManagerMock->expects($this->any())
            ->method('getRepository')
            ->willReturn($repositoryMock);

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

        $lead = new Lead();
        $lead
            ->setPhone('79000000003')
            ->setStatus(Lead::STATUS_EXPECT)
            ->setPrice(10000)
            ->setUser($user)
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

    private function getUser()
    {
        $object = new User();
        $object->setEmail('company1@xoptov.ru');
        return $object;
    }

    private function getWithdraw()
    {

        $user = new User();
        $user
            ->setName('Company 1')
            ->setEmail('company1@xoptov.ru')
            ->setPhone('79000000001')
            ->setPlainPassword(123456);

        $object = new Withdraw();
        $object->setUser($user);

        return $object;
    }

    private function getMember()
    {

        $user = new User();
        $user
            ->setName('Company 1')
            ->setEmail('company1@xoptov.ru')
            ->setPhone('79000000001')
            ->setPlainPassword(123456);

        $object = new Member();
        $object->setUser($user);

        return $object;
    }
}

