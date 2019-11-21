<?php

namespace Tests\Unit\Channels;

use NotificationBundle\ChannelModels\Sms;
use NotificationBundle\Clients\SmsRuClient;
use NotificationBundle\Exceptions\ValidationChannelModelException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zelenin\SmsRu\Api;
use Zelenin\SmsRu\Auth\ApiIdAuth;

class SmsChannelTest extends KernelTestCase
{
    private $client;

    protected function setUp()
    {
        $this->client = self::bootKernel();
    }

    public function testValidation()
    {
        $this->expectException(ValidationChannelModelException::class);
        $validator = $this->client->getContainer()->get('validator');
        $client = new SmsRuClient($validator, new Api(new ApiIdAuth('string')));

        $model = new Sms();
        $model->setBody('Test');
        $model->setFrom('testsender');

        $client->validate($model);

    }
}