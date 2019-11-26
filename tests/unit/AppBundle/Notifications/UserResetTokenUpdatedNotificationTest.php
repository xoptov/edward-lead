<?php

namespace Tests\unit\AppBundle\Notifications;

use AppBundle\Entity\User;
use AppBundle\Notifications\UserResetTokenUpdatedNotification;
use NotificationBundle\Channels\EmailChannel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserResetTokenUpdatedNotificationTest extends TestCase
{
    public function testSend()
    {
        /** @var EmailChannel $emailChanelMock */
        $emailChanelMock = $this->getMockBuilder(EmailChannel::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $this->getMockBuilder(UrlGeneratorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $urlGenerator->expects($this->any())
            ->method('generate')
            ->willReturn('https://symfony.com/doc/');

        $emailChanelMock->expects($this->once())
            ->method('send');

        $object = new User();
        $object->setEmail('company1@xoptov.ru');

        $service = new UserResetTokenUpdatedNotification($emailChanelMock, $urlGenerator);
        $service->send($object);
    }
}
