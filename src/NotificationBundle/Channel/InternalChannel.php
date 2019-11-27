<?php

namespace NotificationBundle\Channel;

use Exception;
use NotificationBundle\Client\Interfaces\InternalClientInterface;
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
     * @param $model
     *
     * @throws Exception
     */
    public function send($model): void
    {
        if (!$model instanceof Notification) {
            throw new Exception('Instance of model should be Notification');
        }

        $this->client->sendToDb($model);
    }
}