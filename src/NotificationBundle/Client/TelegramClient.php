<?php

namespace NotificationBundle\Client;

use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use NotificationBundle\ChannelModel\Telegram;
use NotificationBundle\Client\Interfaces\TelegramClientInterface;
use NotificationBundle\Exception\ValidationChannelModelException;

class TelegramClient extends BaseClient implements TelegramClientInterface
{
    /**
     * @param Telegram $model
     * @return object
     * @throws TelegramException
     * @throws ValidationChannelModelException
     */
    public function sendTelegram(Telegram $model): object
    {
        $this->validate($model);

        return Request::sendMessage([
            'chat_id' => $model->getChatId(),
            'text' => $model->getMessage(),
        ]);

    }

}