<?php

namespace NotificationBundle\Clients\Interfaces;

use NotificationBundle\ChannelModels\Sms;

interface SmsClientInterface
{
    /**
     * @param Sms $model
     * @return object
     */
    public function sendSMS(Sms $model): object;
}