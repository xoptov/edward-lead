<?php

namespace Tests\Unit\Channels;

use AppBundle\Entity\User;
use NotificationBundle\ChannelModels\Telegram;
use NotificationBundle\Channels\TelegramChannel;
use NotificationBundle\Clients\TelegramClient;
use NotificationBundle\Event\ConfigureTelegramEvent;
use NotificationBundle\EventSubscriber\ConfigureTelegramEventSubscriber;
use PHPUnit\Framework\TestCase;

class TelegramChannelTest extends TestCase
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
    public function testSuccessSend()
    {
        $mock = $this->getMockBuilder(TelegramClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->once())
            ->method('sendTelegram');

        $model = new Telegram();
        $model->setChatId('chatId');
        $model->setMessage('some notification message');

        $chanel = new TelegramChannel($mock);

        $chanel->send($model);
    }

}