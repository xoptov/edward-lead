<?php

namespace Tests\Unit\Client;

use Brownie\ESputnik\ESputnik;
use Brownie\ESputnik\HTTPClient\HTTPClient;
use NotificationBundle\Client\EsputnikWebPushClient;
use NotificationBundle\Exception\ValidationNotificationClientException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class WebPushClientTest extends TestCase
{

    public function testSend()
    {
        $httpClient = $this->createMock(HTTPClient::class);
        $eSputnik = $this->createMock(ESputnik::class);

        $eSputnik->expects($this->once())
            ->method('event')
            ->willReturn(true);

        $client = new EsputnikWebPushClient($httpClient, Validation::createValidator(), $eSputnik, 'eventPushKey');

        $client->send([
            "body" => "Example",
            "push_token" => "fk3UW95xaHY:APA91bE0m7h42yYhEwlIpOgL-8n4JzFLiDtKvHB3kdBOKkJCOXSo_-4Fy0COW1emaMFcazEfW0TuYwEozmlDFb47WDW4u7v4Hq85FZHDoIAxPUU24PCX0dE2PcXQ3Wc4Bp9AhlYgeOdf"
        ]);
    }

    public function testSendValidationFail()
    {
        $this->expectException(ValidationNotificationClientException::class);

        $httpClient = $this->createMock(HTTPClient::class);

        $eSputnik = $this->createMock(ESputnik::class);

        $client = new EsputnikWebPushClient($httpClient, Validation::createValidator(), $eSputnik, 'eventPushKey');

        $client->send([
            "body" => "Example",
        ]);
    }
}