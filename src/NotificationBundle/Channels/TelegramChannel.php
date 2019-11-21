<?php

namespace NotificationBundle\Channels;

use Exception;
use NotificationBundle\ChannelModels\ChannelInterface;
use NotificationBundle\ChannelModels\Telegram;
use NotificationBundle\Clients\Interfaces\TelegramClientInterface;

class TelegramChannel implements ChannelInterface
{
    /**
     * @var TelegramClientInterface
     */
    private $client;

    /**
     * TelegramChannel constructor.
     * @param TelegramClientInterface $client
     */
    public function __construct(TelegramClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param ChannelInterface $model
     * @throws Exception
     */
    public function send(ChannelInterface $model): void
    {
        if (!$model instanceof Telegram) {
            throw new Exception('Instance of model should be TelegramModel');
        }

        $this->client->sendTelegram($model);
    }
}