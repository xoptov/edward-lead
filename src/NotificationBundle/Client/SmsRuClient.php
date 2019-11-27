<?php

namespace NotificationBundle\Client;

use NotificationBundle\Client\Interfaces\SmsClientInterface;
use NotificationBundle\Exception\NotificationClientErrorException;
use NotificationBundle\Exception\ValidationNotificationClientException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Zelenin\SmsRu\Api;
use Zelenin\SmsRu\Entity\Sms;
use Zelenin\SmsRu\Exception\Exception;

class SmsRuClient extends Client implements SmsClientInterface
{
    /**
     * @var Api
     */
    private $client;

    public function __construct(ValidatorInterface $validator, Api $client)
    {
        $this->client = $client;
        parent::__construct($validator);
    }

    /**
     * @param array $model
     *
     * @return object
     * @throws Exception
     * @throws NotificationClientErrorException
     * @throws ValidationNotificationClientException
     */
    public function send(array $model): object
    {
       $this->validate($model);

        $sms = new Sms($model['phone'], $model['body']);
        $result = $this->client->smsSend($sms);

        if ($result->code !== 100) {
            throw new NotificationClientErrorException(json_encode($result));
        }

        return $result;
    }

    protected function getValidationRules(): Assert\Collection
    {
        return new Assert\Collection([
            'phone' => new Assert\NotBlank(),
            'body' => new Assert\NotBlank(),
        ]);
    }

}