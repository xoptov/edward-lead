<?php

namespace Tests\Unit\Client;

use Brownie\ESputnik\ESputnik;
use Brownie\ESputnik\HTTPClient\HTTPClient;
use NotificationBundle\Client\EsputnikEmailClient;
use NotificationBundle\Exception\ValidationNotificationClientException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class EmailClientTest extends TestCase
{
    public function testSend()
    {
        $httpClient = $this->createMock(HTTPClient::class);

        $httpClient->expects($this->once())
            ->method('request')
            ->willReturn([]);

        $eSputnik = $this->createMock(ESputnik::class);

        $client = new EsputnikEmailClient($httpClient, Validation::createValidator(), $eSputnik);

        $client->send([
            "to_email" => "2031908",
            "template_id" => "atsutavictor.dev@gmail.com",
            "params" => ["name" => "Test"],
        ]);
    }

    public function testSendValidationFail()
    {
        $this->expectException(ValidationNotificationClientException::class);

        $httpClient = $this->createMock(HTTPClient::class);

        $eSputnik = $this->createMock(ESputnik::class);

        $client = new EsputnikEmailClient($httpClient, Validation::createValidator(), $eSputnik);

        $client->send([
            "template_id" => "atsutavictor.dev@gmail.com",
            "params" => ["name" => "Test"],
        ]);
    }
}