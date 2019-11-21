<?php

namespace NotificationBundle\Clients\Interfaces;

use NotificationBundle\ChannelModels\Telegram;

interface TelegramClientInterface
{
    /**
     * @param Telegram $model
     * @return object
     */
    public function sendTelegram(Telegram $model): object;
}