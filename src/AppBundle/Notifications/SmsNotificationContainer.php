<?php

namespace AppBundle\Notifications;

use AppBundle\Entity\ClientAccount;
use AppBundle\Entity\Withdraw;
use Exception;
use NotificationBundle\Client\Client;

class SmsNotificationContainer
{
    /**
     * @var Client
     */
    private $client;

    /**
     * EmailNotificationContainer constructor.
     *
     * @param Client $smsClient
     */
    public function __construct(Client $smsClient)
    {
        $this->client = $smsClient;
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
            "phone" => $object->getUser()->getEmail(),
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
            "phone" => $object->getUser()->getEmail(),
            "body" => $message,
        ]);
    }
}