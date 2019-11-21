<?php

namespace NotificationBundle\Clients;

use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use NotificationBundle\ChannelModels\Telegram;
use NotificationBundle\Clients\Interfaces\TelegramClientInterface;
use NotificationBundle\Exceptions\ValidationChannelModelException;

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