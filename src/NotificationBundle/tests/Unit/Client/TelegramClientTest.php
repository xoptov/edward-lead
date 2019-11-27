<?php

namespace Tests\Unit\Client;

use AppBundle\Entity\User;
use NotificationBundle\Client\TelegramClient;
use NotificationBundle\Event\ConfigureTelegramEvent;
use NotificationBundle\EventSubscriber\ConfigureTelegramEventSubscriber;
use NotificationBundle\Exception\ValidationNotificationClientException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class TelegramClientTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testSubscriber()
    {
        $user = new User;

        $user->setName('name');
        $user->setPhone('+797812983');
        $user->setEmail('test_telegram@gmail.com');
        $user->setPassword('password');

        $event = new ConfigureTelegramEvent($user);
        $subscriber = new ConfigureTelegramEventSubscriber();
        $subscriber->setTelegramAuthToken($event);

        $token = $user->getTelegramAuthToken();
        $this->assertTrue(is_string($token));
    }

    /**
     * @throws \Exception
     */
    public function testSendValidationFail()
    {
        $this->expectException(ValidationNotificationClientException::class);

        $client = new TelegramClient(Validation::createValidator());

        $client->send([
            "text" => "Test",
        ]);
    }

}