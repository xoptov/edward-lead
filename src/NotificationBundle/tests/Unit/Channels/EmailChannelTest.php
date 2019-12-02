<?php

namespace Tests\Unit\Channels;

use NotificationBundle\ChannelModels\Email;
use NotificationBundle\Channels\EmailChannel;
use NotificationBundle\Clients\EsputnikClient;
use NotificationBundle\Clients\Interfaces\EmailClientInterface;
use NotificationBundle\Clients\Interfaces\InternalClientInterface;
use PHPUnit\Framework\TestCase;

class EmailChannelTest extends TestCase
{
    public function testSuccessSend()
    {
        $mock = $this->getMockBuilder(InternalClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->once())
            ->method('sendToDb');

        $model = new Email();

        $model->setTemplateId('2031908');
        $model->setToEmail('atsutavictor.dev@gmail.com');
        $model->setParams(["name" => "Test"]);

        $chanel = new EmailChannel($mock);

        $chanel->send($model);
    }
}