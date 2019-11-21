<?php

namespace Tests\Functionsl\Channels;

use NotificationBundle\ChannelModels\WebPush;
use NotificationBundle\Channels\WebPushChannel;
use NotificationBundle\Clients\EsputnikClient;
use NotificationBundle\tests\Functional\BaseFunctional;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class WebPushChannelTest extends BaseFunctional
{
    public function testSuccessSend()
    {

        $mock = $this->getMockBuilder(EsputnikClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->once())
            ->method('sendWebPush');

        $this->client->getContainer()->set(EsputnikClient::class, $mock);

        $model = new WebPush();

        $model->setBody('some test data');
        $model->setTitle('Example');
        $model->setLink('https://cabinet.edward-lead.ru');
        $model->setPushToken('fk3UW95xaHY:APA91bE0m7h42yYhEwlIpOgL-8n4JzFLiDtKvHB3kdBOKkJCOXSo_-4Fy0COW1emaMFcazEfW0TuYwEozmlDFb47WDW4u7v4Hq85FZHDoIAxPUU24PCX0dE2PcXQ3Wc4Bp9AhlYgeOdf');

        $chanel = $this->client->getContainer()->get(WebPushChannel::class);

        $chanel->send($model);
    }
}