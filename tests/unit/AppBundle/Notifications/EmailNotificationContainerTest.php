<?php

namespace Tests\unit\AppBundle\Notifications;

use AppBundle\Entity\ClientAccount;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\Lead;
use AppBundle\Entity\Message;
use AppBundle\Entity\Room;
use AppBundle\Entity\Thread;
use AppBundle\Entity\Trade;
use AppBundle\Entity\User;
use AppBundle\Entity\Withdraw;
use AppBundle\Notifications\EmailNotificationContainer;
use NotificationBundle\Channels\EmailChannel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AccountBalanceApproachingZeroNotificationTest extends TestCase
{
    /**
     * @var EmailNotificationContainer
     */
    private $service;

    /**
     * @var MockObject
     */
    private $emailChanelMock;

    public function setUp()
    {
        $this->emailChanelMock = $this->getMockBuilder(EmailChannel::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $this->getMockBuilder(UrlGeneratorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new EmailNotificationContainer($this->emailChanelMock, $urlGenerator, 'admin@mail.com');
    }

    public function testAccountBalanceApproachingZero()
    {
        $this->emailChanelMock->expects($this->once())->method('send');
        $this->service->accountBalanceApproachingZero($this->getAccount());
    }

    public function testAccountBalanceLowerThenMinimal()
    {
        $this->emailChanelMock->expects($this->once())->method('send');
        $this->service->accountBalanceLowerThenMinimal($this->getAccount());
    }

    public function testInvoiceProcessed()
    {
        $this->emailChanelMock->expects($this->once())->method('send');

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
        $this->emailChanelMock->expects($this->once())->method('send');
        $this->service->leadNewPlaced($this->getLead());
    }

    public function testNoVisitTooLong()
    {
        $this->emailChanelMock->expects($this->once())->method('send');
        $this->service->noVisitTooLong($this->getLead());
    }

    public function testMessageCreated()
    {
        $this->emailChanelMock->expects($this->once())->method('send');
        $this->service->messageCreated($this->getMessage());
    }

    public function testMessageSupportReply()
    {
        $this->emailChanelMock->expects($this->once())->method('send');
        $this->service->messageSupportReply($this->getMessage());
    }

    public function testNewUserRegistered()
    {
        $this->emailChanelMock->expects($this->once())->method('send');
        $this->service->newUserRegistered($this->getUser());
    }

    public function testTradeProceeding()
    {
        $this->emailChanelMock->expects($this->once())->method('send');

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
        $this->emailChanelMock->expects($this->once())->method('send');
        $this->service->userApiTokenChanged($this->getUser());
    }

    public function testUserPasswordChanged()
    {
        $this->emailChanelMock->expects($this->once())->method('send');
        $this->service->userPasswordChanged($this->getUser());
    }

    public function testUserResetTokenUpdated()
    {
        $this->emailChanelMock->expects($this->once())->method('send');
        $this->service->userResetTokenUpdated($this->getUser());
    }

    public function testWithdrawAccepted()
    {
        $this->emailChanelMock->expects($this->once())->method('send');
        $this->service->withdrawAccepted($this->getWithdraw());
    }

    public function testWithdrawAdmin()
    {
        $this->emailChanelMock->expects($this->once())->method('send');
        $this->service->withdrawAdmin($this->getWithdraw());
    }

    public function testWithdrawRejected()
    {
        $this->emailChanelMock->expects($this->once())->method('send');
        $this->service->withdrawRejected($this->getWithdraw());
    }

    public function testWithdrawUser()
    {
        $this->emailChanelMock->expects($this->once())->method('send');
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
}
