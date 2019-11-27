<?php

namespace NotificationBundle\Channel;

use Exception;
use NotificationBundle\ChannelModel\WebPush;
use NotificationBundle\Client\Interfaces\WebPushClientInterface;

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
     * @param $model
     *
     * @throws Exception
     */
    public function send($model): void
    {

        if(!$model instanceof WebPush){
            throw new Exception('Instance of model should be WebPush');
        }

        $this->client->sendWebPush($model);
    }
}