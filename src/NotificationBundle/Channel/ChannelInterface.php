<?php

namespace NotificationBundle\Channel;

interface ChannelInterface
{
    public function send($model): void;
}