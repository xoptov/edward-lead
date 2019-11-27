<?php

namespace Tests\Unit\Channels;

use NotificationBundle\ChannelModel\Sms;
use NotificationBundle\Channel\SmsChannel;
use NotificationBundle\Client\SmsRuClient;
use PHPUnit\Framework\TestCase;

class SmsChannelTest extends TestCase
{

    public function testSuccessSend()
    {
        $mock = $this->createMock(SmsRuClient::class);

        $mock->expects($this->once())
            ->method('sendSMS');

        $model = new Sms();

        $model->setPhone('+79787151111');
        $model->setBody('Test');
        $model->setFrom('testsender');

        $chanel = new SmsChannel($mock);

        $chanel->send($model);
    }
}