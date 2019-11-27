<?php

namespace NotificationBundle\Client\Interfaces;

use NotificationBundle\ChannelModel\Sms;

interface SmsClientInterface
{
    /**
     * @param Sms $model
     * @return object
     */
    public function sendSMS(Sms $model): object;
}