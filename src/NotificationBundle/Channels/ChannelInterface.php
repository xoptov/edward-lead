<?php

namespace NotificationBundle\Channels;

use NotificationBundle\ChannelModels\ChannelInterface;

interface ChannelInterface
{
    public function send(ChannelInterface $model): void;
}