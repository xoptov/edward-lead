<?php

namespace Tests\Unit\Client;

use NotificationBundle\Client\SmsRuClient;
use NotificationBundle\Exception\ValidationNotificationClientException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Zelenin\SmsRu\Api;

class SmsClientTest extends TestCase
{

    public function testSend()
    {
        $api = $this->createMock(Api::class);
        $client = new SmsRuClient(Validation::createValidator(), $api);

        $response = new \stdClass();
        $response->code = 100;

        $api->expects($this->once())
            ->method('smsSend')
            ->willReturn($response);

        $client->send([
            "phone" => "+79787151111",
            "body" => "Test",
        ]);
    }

    public function testSendValidationFail()
    {
        $this->expectException(ValidationNotificationClientException::class);

        $api = $this->createMock(Api::class);
        $client = new SmsRuClient(Validation::createValidator(), $api);

        $client->send([
            "body" => "Test",
        ]);
    }
}