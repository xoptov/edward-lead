<?php

namespace NotificationBundle\Client\Interfaces;

use NotificationBundle\ChannelModel\Email;

interface EmailClientInterface
{
    /**
     * @param Email $model
     * @return object
     */
    public function sendEmail(Email $model): object;
}