<?php

namespace NotificationBundle\Clients\Interfaces;

use NotificationBundle\Entity\Notification;

interface InternalClientInterface
{
    /**
     * @param Notification $model
     * @return void
     */
    public function sendToDb(Notification $model): void;
}