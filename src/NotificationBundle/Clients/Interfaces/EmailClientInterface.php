<?php

namespace NotificationBundle\Clients\Interfaces;

use NotificationBundle\ChannelModels\Email;

interface EmailClientInterface
{
    /**
     * @param Email $model
     * @return array
     */
    public function sendEmail(Email $model): array;
}