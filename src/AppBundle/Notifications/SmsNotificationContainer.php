<?php

namespace AppBundle\Notifications;

use AppBundle\Entity\ClientAccount;
use AppBundle\Entity\Message;
use AppBundle\Entity\User;
use AppBundle\Entity\Withdraw;
use Exception;
use NotificationBundle\Channels\SmsChannel;
use NotificationBundle\Client\Client;

class SmsNotificationContainer
{
    /**
     * @var SmsChannel
     */
    private $client;

    /**
     * EmailNotificationContainer constructor.
     *
     * @param SmsChannel $client
     */
    public function __construct(SmsChannel $client)
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
        $message = 'Ваш баланс приблежается к нулю. Не забудьте его пополнить. Текущий - ' . $object->getBalance();

        $this->client->send([
            "phone" => $object->getUser()->getPhone(),
            "body" => $message,
        ]);
    }

    /**
     * @param Withdraw $object
     *
     * @throws Exception
     */
    public function withdrawUser(Withdraw $object): void
    {
        $message = 'Запрос на вывод средств отправлен. Ожидайте ответа администрации';

        $this->client->send([
            "phone" => $object->getUser()->getPhone(),
            "body" => $message,
        ]);
    }
}