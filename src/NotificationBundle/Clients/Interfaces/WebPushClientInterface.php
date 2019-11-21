<?php

namespace NotificationBundle\Clients\Interfaces;

use NotificationBundle\ChannelModels\WebPush;

interface WebPushClientInterface
{
    /**
     * @param WebPush $model
     * @return object
     */
    public function sendWebPush(WebPush $model): object;

}