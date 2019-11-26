<?php

namespace Tests\unit\AppBundle\Notifications;

use AppBundle\Entity\User;
use AppBundle\Entity\Withdraw;
use AppBundle\Notifications\WithdrawAdminNotification;
use NotificationBundle\Channels\EmailChannel;
use PHPUnit\Framework\TestCase;

class WithdrawAdminNotificationTest extends TestCase
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

        $object = new Withdraw();
        $object->setUser($user);

        $service = new WithdrawAdminNotification($emailChanelMock, 'admin@mail.com');
        $service->send($object);
    }
}
