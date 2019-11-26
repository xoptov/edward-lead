<?php

namespace Tests\unit\AppBundle\Notifications;

use AppBundle\Entity\Invoice;
use AppBundle\Entity\User;
use AppBundle\Notifications\InvoiceProcessedNotification;
use NotificationBundle\Channels\EmailChannel;
use PHPUnit\Framework\TestCase;

class InvoiceProcessedNotificationTest extends TestCase
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

        $invoice = new Invoice();
        $invoice
            ->setUser($user)
            ->setAmount(100)
            ->setDescription('Some Description');

        $service = new InvoiceProcessedNotification($emailChanelMock);
        $service->send($invoice);
    }
}
