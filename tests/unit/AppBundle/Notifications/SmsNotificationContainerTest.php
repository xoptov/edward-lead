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

class SmsNotificationContainerTest extends BaseNotificationContainerTestCase
{
    /**
     * @var EmailNotificationContainer
     */
    private $service;

    /**
     * @var Client
     */
    private $smsClientMock;

    public function setUp()
    {
        $this->smsClientMock = $this->createMock(Client::class);
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
}
