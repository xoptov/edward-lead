<?php

namespace NotificationBundle\Client;

use NotificationBundle\Exception\NotificationClientErrorException;
use NotificationBundle\Exception\ValidationNotificationClientException;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Zelenin\SmsRu\Api;
use Zelenin\SmsRu\Entity\Sms;
use Zelenin\SmsRu\Exception\Exception;

class SmsRuClient extends Client
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

    /**
     * @return Collection
     */
    protected function getValidationRules(): Collection
    {
        return new Collection([
            'phone' => new NotBlank(),
            'body' => new NotBlank(),
        ]);
    }

}