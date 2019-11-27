<?php

namespace NotificationBundle\Channel;

use Exception;
use NotificationBundle\ChannelModel\Telegram;
use NotificationBundle\Client\Interfaces\TelegramClientInterface;

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
     * @param $model
     *
     * @throws Exception
     */
    public function send($model): void
    {
        if (!$model instanceof Telegram) {
            throw new Exception('Instance of model should be TelegramModel');
        }

        $this->client->sendTelegram($model);
    }
}