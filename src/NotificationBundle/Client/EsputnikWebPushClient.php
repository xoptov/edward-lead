<?php

namespace NotificationBundle\Client;

use Brownie\ESputnik\ESputnik;
use Brownie\ESputnik\HTTPClient\HTTPClient;
use Brownie\ESputnik\Model\Event;
use NotificationBundle\Client\Interfaces\WebPushClientInterface;
use NotificationBundle\Exception\ValidationNotificationClientException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EsputnikWebPushClient extends EsputnikClient implements WebPushClientInterface
{
    /**
     * @var string
     */
    private $eventPushKey;
    /**
     * @var ESputnik
     */
    private $client;

    /**
     * EsputnikWebPushClient constructor.
     *
     * @param HTTPClient         $httpClient
     * @param ValidatorInterface $validator
     * @param ESputnik           $sputnik
     * @param string             $eventPushKey
     */
    public function __construct(HTTPClient $httpClient, ValidatorInterface $validator, ESputnik $sputnik, string $eventPushKey)
    {
        parent::__construct($httpClient, $validator);
        $this->eventPushKey = $eventPushKey;
        $this->client = $sputnik;
    }

    /**
     * @param array $model
     *
     * @return bool
     * @throws ValidationNotificationClientException
     */
    public function send(array $model): bool
    {
        $this->validate($model);

        $params = [
            [
                "name" => "body",
                "value" => $model['body'],
            ],
            [
                "name" => "link",
                "value" => $model['link'],
            ],
            [
                "name" => "pushToken",
                "value" => $model['push_token'],
            ]
        ];

        $result = $this->client->event(new Event([
            'eventTypeKey' => $this->eventPushKey,
            'keyValue' => $model['push_token'],
            'params' => $params
        ]));

        return $result;
    }

    protected function getValidationRules(): Assert\Collection
    {
        return new Assert\Collection([
            'body' => new Assert\NotBlank(),
            'link' => new Assert\NotBlank(),
            'push_token' => new Assert\NotBlank(),
        ]);
    }

}