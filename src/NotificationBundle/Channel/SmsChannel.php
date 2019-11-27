<?php

namespace NotificationBundle\Channel;

use Exception;
use NotificationBundle\ChannelModel\Sms;
use NotificationBundle\Client\Interfaces\SmsClientInterface;

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
     * @param $model
     *
     * @throws Exception
     */
    public function send($model): void
    {

        if(!$model instanceof Sms){
            throw new Exception('Instance of model should be SmsModel');
        }
        $this->client->sendSMS($model);
    }
}