<?php

namespace Tests\unit\AppBundle\Notifications;

use AppBundle\Entity\ClientAccount;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\Lead;
<<<<<<< HEAD
use AppBundle\Entity\Member;
=======
>>>>>>> 760785bc9199cf97720beb3b1fe73c8a6206d111
use AppBundle\Entity\Message;
use AppBundle\Entity\Room;
use AppBundle\Entity\Thread;
use AppBundle\Entity\Trade;
use AppBundle\Entity\User;
use AppBundle\Entity\Withdraw;
use AppBundle\Notifications\EmailNotificationContainer;
<<<<<<< HEAD
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use NotificationBundle\Client\Client;
=======
use NotificationBundle\Client\Client;
use PHPUnit\Framework\MockObject\MockObject;
>>>>>>> 760785bc9199cf97720beb3b1fe73c8a6206d111
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AccountBalanceApproachingZeroNotificationTest extends TestCase
{
    /**
     * @var EmailNotificationContainer
     */
    private $service;

<<<<<<< HEAD
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
=======
    /**
     * @var MockObject
     */
    private $emailClientMock;

    public function setUp()
    {
        $this->emailClientMock = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $this->getMockBuilder(UrlGeneratorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new EmailNotificationContainer($this->emailClientMock, $urlGenerator, 'admin@mail.com');
>>>>>>> 760785bc9199cf97720beb3b1fe73c8a6206d111
    }

    public function testAccountBalanceApproachingZero()
    {
<<<<<<< HEAD
=======
        $this->emailClientMock->expects($this->once())->method('send');
>>>>>>> 760785bc9199cf97720beb3b1fe73c8a6206d111
        $this->service->accountBalanceApproachingZero($this->getAccount());
    }

    public function testAccountBalanceLowerThenMinimal()
    {
<<<<<<< HEAD
=======
        $this->emailClientMock->expects($this->once())->method('send');
>>>>>>> 760785bc9199cf97720beb3b1fe73c8a6206d111
        $this->service->accountBalanceLowerThenMinimal($this->getAccount());
    }

    public function testInvoiceProcessed()
    {
<<<<<<< HEAD
=======
        $this->emailClientMock->expects($this->once())->method('send');

>>>>>>> 760785bc9199cf97720beb3b1fe73c8a6206d111
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
<<<<<<< HEAD
=======
        $this->emailClientMock->expects($this->once())->method('send');
>>>>>>> 760785bc9199cf97720beb3b1fe73c8a6206d111
        $this->service->leadNewPlaced($this->getLead());
    }

    public function testNoVisitTooLong()
    {
<<<<<<< HEAD
        $this->service->noVisitTooLong($this->getMember());
=======
        $this->emailClientMock->expects($this->once())->method('send');
        $this->service->noVisitTooLong($this->getLead());
>>>>>>> 760785bc9199cf97720beb3b1fe73c8a6206d111
    }

    public function testMessageCreated()
    {
<<<<<<< HEAD
=======
        $this->emailClientMock->expects($this->once())->method('send');
>>>>>>> 760785bc9199cf97720beb3b1fe73c8a6206d111
        $this->service->messageCreated($this->getMessage());
    }

    public function testMessageSupportReply()
    {
<<<<<<< HEAD
=======
        $this->emailClientMock->expects($this->once())->method('send');
>>>>>>> 760785bc9199cf97720beb3b1fe73c8a6206d111
        $this->service->messageSupportReply($this->getMessage());
    }

    public function testNewUserRegistered()
    {
<<<<<<< HEAD
=======
        $this->emailClientMock->expects($this->once())->method('send');
>>>>>>> 760785bc9199cf97720beb3b1fe73c8a6206d111
        $this->service->newUserRegistered($this->getUser());
    }

    public function testTradeProceeding()
    {
<<<<<<< HEAD
=======
        $this->emailClientMock->expects($this->once())->method('send');

>>>>>>> 760785bc9199cf97720beb3b1fe73c8a6206d111
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
<<<<<<< HEAD
=======
        $this->emailClientMock->expects($this->once())->method('send');
>>>>>>> 760785bc9199cf97720beb3b1fe73c8a6206d111
        $this->service->userApiTokenChanged($this->getUser());
    }

    public function testUserPasswordChanged()
    {
<<<<<<< HEAD
=======
        $this->emailClientMock->expects($this->once())->method('send');
>>>>>>> 760785bc9199cf97720beb3b1fe73c8a6206d111
        $this->service->userPasswordChanged($this->getUser());
    }

    public function testUserResetTokenUpdated()
    {
<<<<<<< HEAD
=======
        $this->emailClientMock->expects($this->once())->method('send');
>>>>>>> 760785bc9199cf97720beb3b1fe73c8a6206d111
        $this->service->userResetTokenUpdated($this->getUser());
    }

    public function testWithdrawAccepted()
    {
<<<<<<< HEAD
=======
        $this->emailClientMock->expects($this->once())->method('send');
>>>>>>> 760785bc9199cf97720beb3b1fe73c8a6206d111
        $this->service->withdrawAccepted($this->getWithdraw());
    }

    public function testWithdrawAdmin()
    {
<<<<<<< HEAD
=======
        $this->emailClientMock->expects($this->once())->method('send');
>>>>>>> 760785bc9199cf97720beb3b1fe73c8a6206d111
        $this->service->withdrawAdmin($this->getWithdraw());
    }

    public function testWithdrawRejected()
    {
<<<<<<< HEAD
=======
        $this->emailClientMock->expects($this->once())->method('send');
>>>>>>> 760785bc9199cf97720beb3b1fe73c8a6206d111
        $this->service->withdrawRejected($this->getWithdraw());
    }

    public function testWithdrawUser()
    {
<<<<<<< HEAD
=======
        $this->emailClientMock->expects($this->once())->method('send');
>>>>>>> 760785bc9199cf97720beb3b1fe73c8a6206d111
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
<<<<<<< HEAD

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

=======
}
>>>>>>> 760785bc9199cf97720beb3b1fe73c8a6206d111
