<?php

namespace AppBundle\Notifications;

use AppBundle\Entity\ClientAccount;
use AppBundle\Entity\Lead;
use AppBundle\Entity\Member;
use AppBundle\Entity\Message;
use AppBundle\Entity\Trade;
use AppBundle\Entity\User;
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

        $user = $object->getSender();

        if(!$user instanceof User) return;

        $this->client->send([
            "body" => "У вас имееться новое сообщение от службы поддержки",
            "push_token" => $user->getWebPushToken()
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
            "body" => "В комнате {$object->getRoom()->getId()} появился новый лид",
            "push_token" => $object->getBuyer()->getWebPushToken()
        ]);
    }

    /**
     * @param Lead $object
     *
     * @throws Exception
     */
    public function leadExpectTooLong(Lead $object): void
    {
        foreach ($object->getRoom()->getMembers() as $member){

            /** @var Member $member */
            if(!$member->getUser()->isWebmaster()) continue;

            $this->client->send([
                "body" => "Лид {$object->getId()} уже больше 2 часов находиться в статусе Ожидания",
                "push_token" => $member->getUser()->getWebPushToken()
            ]);

        }


    }

    /**
     * @param Lead $object
     *
     * @throws Exception
     */
    public function leadInWorkTooLong(Lead $object): void
    {
        $this->client->send([
            "body" => "Лид {$object->getId()} уже более 24 часов находиться в статусе - В Работе",
            "push_token" => $object->getBuyer()->getWebPushToken()
        ]);
    }
}