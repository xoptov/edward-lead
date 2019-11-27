<?php

namespace NotificationBundle\Client\Interfaces;

use NotificationBundle\ChannelModel\WebPush;

interface WebPushClientInterface
{
    /**
     * @param WebPush $model
     * @return object
     */
    public function sendWebPush(WebPush $model): object;

}