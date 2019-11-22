<?php

namespace Tests\Unit\Channels;

use NotificationBundle\ChannelModels\Sms;
use NotificationBundle\Channels\SmsChannel;
use NotificationBundle\Clients\SmsRuClient;
use PHPUnit\Framework\TestCase;
use Zelenin\SmsRu\Api;
use Zelenin\SmsRu\Auth\ApiIdAuth;

class SmsChannelTest extends TestCase
{

    public function testSuccessSend()
    {
        $mock = $this->getMockBuilder(SmsRuClient::class)
            ->disableOriginalConstructor()
            ->getMock();

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