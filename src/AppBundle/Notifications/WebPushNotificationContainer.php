<?php

namespace AppBundle\Notifications;

use AppBundle\Entity\ClientAccount;
use AppBundle\Entity\Lead;
use AppBundle\Entity\Message;
use AppBundle\Entity\Trade;
use Exception;
use NotificationBundle\Client\Client;

class WebPushNotificationContainer
{
    /**
     * @var Client
     */
    private $client;

    /**
     * WebPushNotificationContainer constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param ClientAccount $object
     *
     * @throws Exception
     */
    public function accountBalanceApproachingZero(ClientAccount $object): void
    {
        $message = "Ваш баланс приблежаетсья к нулю. Не забудьте его пополнить. Текущий - " . $object->getBalance();

        $this->client->send([
            "body" => $message,
            "push_token" => $object->getUser()->getWebPushToken()
        ]);
    }



    /**
     * @param Message $object
     *
     * @throws Exception
     */
    public function messageSupportReply(Message $object): void
    {
        $this->client->send([
            "body" => "",
            "push_token" => ""
        ]);
    }

    /**
     * @param Lead $object
     *
     * @throws Exception
     */
    public function leadNewPlaced(Lead $object): void
    {
        $this->client->send([
            "body" => "",
            "push_token" => ""
        ]);
    }

    /**
     * @param Trade $object
     *
     * @throws Exception
     */
    public function tradeProceeding(Trade $object): void
    {
        $this->client->send([
            "body" => "",
            "push_token" => ""
        ]);
    }

    /**
     * @param Message $object
     *
     * @throws Exception
     */
    public function messageCreated(Message $object): void
    {
        // TODO
//        $this->client->send([
//            "body" => "",
//            "push_token" => $object->setSender()->getWebPushToken()
//        ]);
    }

    /**
     * @param Lead $object
     *
     * @throws Exception
     */
    public function leadExpectTooLong(Lead $object): void
    {
//        $this->client->send([
//            "body" => "",
//            "push_token" => $object->getUser()->getWebPushToken()
//        ]);
    }

    /**
     * @param Lead $object
     *
     * @throws Exception
     */
    public function leadInWorkTooLong(Lead $object): void
    {
//        $this->client->send([
//            "body" => "",
//            "push_token" => $object->getUser()->getWebPushToken()
//        ]);
    }
}