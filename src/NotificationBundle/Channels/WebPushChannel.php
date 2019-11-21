<?php

namespace NotificationBundle\Channels;

use Exception;
use NotificationBundle\ChannelModels\ChannelInterface;
use NotificationBundle\ChannelModels\WebPush;
use NotificationBundle\Clients\Interfaces\EmailClientInterface;
use NotificationBundle\Clients\Interfaces\WebPushClientInterface;

class WebPushChannel implements ChannelInterface
{
    /**
     * @var WebPushClientInterface
     */
    private $client;

    /**
     * WebPushChannel constructor.
     * @param WebPushClientInterface $client
     */
    public function __construct(WebPushClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param ChannelInterface $model
     * @throws Exception
     */
    public function send(ChannelInterface $model): void
    {

        if(!$model instanceof WebPush){
            throw new Exception('Instance of model should be WebPush');
        }

        $this->client->sendWebPush($model);
    }
}