<?php

namespace NotificationBundle\Channels;

use Exception;
use NotificationBundle\ChannelModels\ChannelInterface;
use NotificationBundle\Clients\Interfaces\InternalClientInterface;
use NotificationBundle\Entity\Notification;

class InternalChannel implements ChannelInterface
{
    /**
     * @var InternalClientInterface
     */
    private $client;

    /**
     * @param InternalClientInterface $client
     */
    public function __construct(InternalClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param ChannelInterface $model
     * @throws Exception
     */
    public function send(ChannelInterface $model): void
    {
        if (!$model instanceof Notification) {
            throw new Exception('Instance of model should be Notification');
        }

        $this->client->sendToDb($model);
    }
}