<?php

namespace Tests\unit\AppBundle\Notifications;

use AppBundle\Entity\Message;
use AppBundle\Entity\Thread;
use AppBundle\Entity\User;
use AppBundle\Notifications\MessageCreatedNotification;
use NotificationBundle\Channels\EmailChannel;
use PHPUnit\Framework\TestCase;

class MessageCreatedNotificationTest extends TestCase
{
    public function testSend()
    {
        /** @var EmailChannel $emailChanelMock */
        $emailChanelMock = $this->getMockBuilder(EmailChannel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $emailChanelMock->expects($this->once())
            ->method('send');

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

        $service = new MessageCreatedNotification($emailChanelMock, 'admin@mail.com');
        $service->send($object);
    }
}
