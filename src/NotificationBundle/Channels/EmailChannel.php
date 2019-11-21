<?php

namespace NotificationBundle\Channels;

use Exception;
use NotificationBundle\ChannelModels\ChannelInterface;
use NotificationBundle\ChannelModels\Email;
use NotificationBundle\Clients\Interfaces\EmailClientInterface;

class EmailChannel implements ChannelInterface
{
    /**
     * @var EmailClientInterface
     */
    private $client;

    /**
     * EmailChannel constructor.
     * @param EmailClientInterface $client
     */
    public function __construct(EmailClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param ChannelInterface $model
     * @throws Exception
     */

    public function send(ChannelInterface $model): void
    {

        if (!$model instanceof Email) {
            throw new Exception('Instance of model should be Email');
        }

        $this->client->sendEmail($model);
    }
}