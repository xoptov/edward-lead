<?php

namespace NotificationBundle\Clients;

use NotificationBundle\ChannelModels\Sms as SmsModel;
use NotificationBundle\Clients\Interfaces\SmsClientInterface;
use NotificationBundle\Exceptions\NoApiClientException;
use NotificationBundle\Exceptions\NotificationClientErrorException;
use NotificationBundle\Exceptions\ValidationChannelModelException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Zelenin\SmsRu\Api;
use Zelenin\SmsRu\Entity\Sms;
use Zelenin\SmsRu\Exception\Exception;

class SmsRuClient extends BaseClient implements SmsClientInterface
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
     * @param SmsModel $model
     * @return object
     * @throws NotificationClientErrorException
     * @throws ValidationChannelModelException
     * @throws Exception
     */
    public function sendSMS(SmsModel $model): object
    {
       $this->validate($model);

        $sms = new Sms($model->getPhone(), $model->getBody());
        $result = $this->client->smsSend($sms);

        if ($result->code !== 100) {
            throw new NotificationClientErrorException(json_encode($result));
        }

        return $result;
    }

}