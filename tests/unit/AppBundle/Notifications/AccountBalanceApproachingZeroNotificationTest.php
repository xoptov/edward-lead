<?php

namespace Tests\unit\AppBundle\Notifications;

use AppBundle\Entity\ClientAccount;
use AppBundle\Entity\User;
use AppBundle\Notifications\AccountBalanceApproachingZeroNotification;
use NotificationBundle\Channels\EmailChannel;
use PHPUnit\Framework\TestCase;

class AccountBalanceApproachingZeroNotificationTest extends TestCase
{

    public function testSend()
    {
        /** @var EmailChannel $emailChanelMock */
        $emailChanelMock = $this->getMockBuilder(EmailChannel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $emailChanelMock->expects($this->once())
            ->method('send');

        $user = new User();
        $user
            ->setName('Company 1')
            ->setEmail('company1@xoptov.ru')
            ->setPhone('79000000001')
            ->setPlainPassword(123456);

        $account = new ClientAccount();
        $account
            ->setUser($user);

        $service = new AccountBalanceApproachingZeroNotification($emailChanelMock);
        $service->send($account);
    }
}
