<?php

namespace Tests\unit\AppBundle\Notifications;

use AppBundle\Entity\User;
use AppBundle\Notifications\UserPasswordChangedNotification;
use NotificationBundle\Channels\EmailChannel;
use PHPUnit\Framework\TestCase;

class UserPasswordChangedNotificationTest extends TestCase
{
    public function testSend()
    {
        /** @var EmailChannel $emailChanelMock */
        $emailChanelMock = $this->getMockBuilder(EmailChannel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $emailChanelMock->expects($this->once())
            ->method('send');

        $object = new User();
        $object->setEmail('company1@xoptov.ru');

        $service = new UserPasswordChangedNotification($emailChanelMock);
        $service->send($object);
    }
}
