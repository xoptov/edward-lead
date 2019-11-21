<?php

namespace NotificationBundle\Channels;

use Exception;
use NotificationBundle\ChannelModels\ChannelInterface;
use NotificationBundle\ChannelModels\Sms;
use NotificationBundle\Clients\Interfaces\SmsClientInterface;

class SmsChannel implements ChannelInterface
{
    /**
     * @var SmsClientInterface
     */
    private $client;

    /**
     * SmsChannel constructor.
     * @param SmsClientInterface $client
     */
    public function __construct(SmsClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param ChannelInterface $model
     * @throws Exception
     */
    public function send(ChannelInterface $model): void
    {

        if(!$model instanceof Sms){
            throw new Exception('Instance of model should be SmsModel');
        }
        $this->client->sendSMS($model);
    }
}