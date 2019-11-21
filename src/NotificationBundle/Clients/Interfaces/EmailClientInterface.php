<?php

namespace NotificationBundle\Clients\Interfaces;

use NotificationBundle\ChannelModels\Email;

interface EmailClientInterface
{
    /**
     * @param Email $model
     * @return object
     */
    public function sendEmail(Email $model): object;
}