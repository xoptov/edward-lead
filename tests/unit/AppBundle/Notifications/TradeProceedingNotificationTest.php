<?php

namespace Tests\unit\AppBundle\Notifications;

use AppBundle\Entity\Lead;
use AppBundle\Entity\Room;
use AppBundle\Entity\Trade;
use AppBundle\Entity\User;
use AppBundle\Notifications\TradeProceedingNotification;
use NotificationBundle\Channels\EmailChannel;
use PHPUnit\Framework\TestCase;

class TradeProceedingNotificationTest extends TestCase
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

        $service = new TradeProceedingNotification($emailChanelMock, 'admin@mail.com');
        $service->send($object);
    }
}
