<?php

namespace NotificationBundle\Client\Interfaces;

use NotificationBundle\ChannelModel\Telegram;

interface TelegramClientInterface
{
    /**
     * @param Telegram $model
     * @return object
     */
    public function sendTelegram(Telegram $model): object;
}