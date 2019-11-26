<?php

namespace Tests\unit\AppBundle\Notifications;

use AppBundle\Entity\ClientAccount;
use AppBundle\Entity\User;
use AppBundle\Notifications\AccountBalanceLowerThenMinimalNotification;
use NotificationBundle\Channels\EmailChannel;
use PHPUnit\Framework\TestCase;

class AccountBalanceLowerThenMinimalNotificationTest extends TestCase
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

        $service = new AccountBalanceLowerThenMinimalNotification($emailChanelMock);
        $service->send($account);
    }
}
