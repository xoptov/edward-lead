<?php

namespace NotificationBundle\Client;

use Brownie\ESputnik\Exception\InvalidCodeException;
use Brownie\ESputnik\Exception\JsonException;
use Brownie\ESputnik\HTTPClient\HTTPClient;
use NotificationBundle\Client\Interfaces\EmailClientInterface;
use NotificationBundle\Exception\ValidationNotificationClientException;
use Symfony\Component\Validator\Constraints as Assert;

class EsputnikEmailClient extends EsputnikClient implements EmailClientInterface
{
    const SMART_SEND_URL_TEMPLATE = 'message/{tempalteId}/smartsend';

    /**
     * @param array $model
     *
     * @return array
     * @throws InvalidCodeException
     * @throws JsonException
     * @throws ValidationNotificationClientException
     */
    public function send(array $model): array
    {
        $this->validate($model);

        $data = [
            "recipients" => [
                "jsonParam" => json_encode($model['params']),
                "locator" => $model['to_email']
            ],
            "email" => true,
            "fromName" => null
        ];

        $url = str_replace('{tempalteId}', $model['template_id'], self::SMART_SEND_URL_TEMPLATE);

        $result = $this->httpClient->request(
            HTTPClient::HTTP_CODE_200,
            $url,
            $data,
            HTTPClient::HTTP_METHOD_POST
        );

        return $result;
    }

    protected function getValidationRules(): Assert\Collection
    {
        return new Assert\Collection([
            'to_email' => new Assert\NotBlank(),
            'template_id' => new Assert\NotBlank(),
            'params' => new Assert\NotBlank(),
        ]);
    }

}