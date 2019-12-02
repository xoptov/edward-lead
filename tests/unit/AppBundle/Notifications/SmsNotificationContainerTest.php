<?php

namespace Tests\unit\AppBundle\Notifications;

use AppBundle\Entity\ClientAccount;
use AppBundle\Entity\User;
use AppBundle\Entity\Withdraw;
use AppBundle\Notifications\EmailNotificationContainer;
use AppBundle\Notifications\SmsNotificationContainer;
use NotificationBundle\Client\Client;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SmsNotificationContainerTest extends TestCase
{
    /**
     * @var EmailNotificationContainer
     */
    private $service;

    /**
     * @var MockObject
     */
    private $smsClientMock;

    public function setUp()
    {
        $this->smsClientMock = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new SmsNotificationContainer($this->smsClientMock);
    }

    public function testAccountBalanceApproachingZero()
    {
        $this->smsClientMock->expects($this->once())->method('send');
        $this->service->accountBalanceApproachingZero($this->getAccount());
    }

    public function testWithdrawUser()
    {
        $this->smsClientMock->expects($this->once())->method('send');
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
