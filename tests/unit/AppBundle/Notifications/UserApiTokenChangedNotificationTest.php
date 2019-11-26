<?php

namespace Tests\unit\AppBundle\Notifications;

use AppBundle\Entity\User;
use AppBundle\Notifications\UserApiTokenChangedNotification;
use NotificationBundle\Channels\EmailChannel;
use PHPUnit\Framework\TestCase;

class UserApiTokenChangedNotificationTest extends TestCase
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

        $service = new UserApiTokenChangedNotification($emailChanelMock);
        $service->send($object);
    }
}
