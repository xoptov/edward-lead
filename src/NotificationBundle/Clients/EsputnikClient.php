<?php

namespace NotificationBundle\Clients;

use Brownie\ESputnik\ESputnik;
use Brownie\ESputnik\Exception\InvalidCodeException;
use Brownie\ESputnik\Exception\JsonException;
use Brownie\ESputnik\HTTPClient\HTTPClient;
use Brownie\ESputnik\Model\Event;
use NotificationBundle\ChannelModels\Email;
use NotificationBundle\ChannelModels\WebPush;
use NotificationBundle\Clients\Interfaces\EmailClientInterface;
use NotificationBundle\Clients\Interfaces\WebPushClientInterface;
use NotificationBundle\Exceptions\ValidationChannelModelException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EsputnikClient extends BaseClient implements WebPushClientInterface, EmailClientInterface
{
    const SMART_SEND_URL_TEMPLATE = 'message/{tempalteId}/smartsend';

    /**
     * @var ESputnik
     */
    private $client;

    /**
     * @var string
     */
    private $eventPushKey;

    /**
     * @var HTTPClient
     */
    private $httpClient;

    /**
     * EsputnikClient constructor.
     * @param HTTPClient $httpClient
     * @param string $eventPushKey
     * @param ValidatorInterface $validator
     * @param ESputnik $sputnik
     */
    public function __construct(HTTPClient $httpClient, string $eventPushKey, ValidatorInterface $validator, ESputnik $sputnik)
    {
        $this->httpClient = $httpClient;
        $this->client = $sputnik;
        $this->eventPushKey = $eventPushKey;

        parent::__construct($validator);
    }

    /**
     * @param Email $model
     * @return object
     * @throws ValidationChannelModelException
     * @throws InvalidCodeException
     * @throws JsonException
     */
    public function sendEmail(Email $model): object
    {

        $this->validate($model);

        $data = [
            "recipients" => [
                "jsonParam" => json_encode($model->getParams()),
                "locator" => $model->getToEmail()
            ],
            "email" => true,
            "fromName" => null
        ];

        $url = str_replace('{tempalteId}', $model->getTemplateId(), self::SMART_SEND_URL_TEMPLATE);

        $result = $this->httpClient->request(
            HTTPClient::HTTP_CODE_200,
            $url,
            $data,
            HTTPClient::HTTP_METHOD_POST
        );

        return $result;
    }

    /**
     * @param WebPush $model
     * @return object
     * @throws ValidationChannelModelException
     */
    public function sendWebPush(WebPush $model): object
    {
        $this->validate($model);

        $params = [
            [
                "name" => "title",
                "value" => $model->getTitle(),
            ],
            [
                "name" => "body",
                "value" => $model->getBody(),
            ],
            [
                "name" => "link",
                "value" => $model->getLink(),
            ],
            [
                "name" => "pushToken",
                "value" => $model->getPushToken(),
            ]
        ];

        $result = $this->client->event(new Event([
            'eventTypeKey' => $this->eventPushKey,
            'keyValue' => $model->getPushToken(),
            'params' => $params
        ]));

        return $result;
    }

}