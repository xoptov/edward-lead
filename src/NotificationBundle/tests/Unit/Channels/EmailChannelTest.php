<?php

namespace Tests\Unit\Channels;

use NotificationBundle\ChannelModel\Email;
use NotificationBundle\Channel\EmailChannel;
use NotificationBundle\Client\EsputnikClient;
use PHPUnit\Framework\TestCase;

class EmailChannelTest extends TestCase
{
    public function testSuccessSend()
    {
        $mock = $this->createMock(EsputnikClient::class);

        $mock->expects($this->once())
            ->method('sendEmail');

        $model = new Email();

        $model->setTemplateId('2031908');
        $model->setToEmail('atsutavictor.dev@gmail.com');
        $model->setParams(["name" => "Test"]);

        $chanel = new EmailChannel($mock);

        $chanel->send($model);
    }
}