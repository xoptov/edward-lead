<?php

namespace NotificationBundle\Channel;

use Exception;
use NotificationBundle\ChannelModel\Email;
use NotificationBundle\Client\Interfaces\EmailClientInterface;

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
     * @param $model
     *
     * @throws Exception
     */

    public function send($model): void
    {

        if (!$model instanceof Email) {
            throw new Exception('Instance of model should be Email');
        }

        $this->client->sendEmail($model);
    }
}