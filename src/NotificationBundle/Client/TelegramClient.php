<?php

namespace NotificationBundle\Client;

use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use NotificationBundle\Client\Interfaces\TelegramClientInterface;
use NotificationBundle\Exception\ValidationNotificationClientException;
use Symfony\Component\Validator\Constraints as Assert;

class TelegramClient extends Client implements TelegramClientInterface
{
    /**
     * @param array $model
     *
     * @return object
     * @throws TelegramException
     * @throws ValidationNotificationClientException
     */
    public function send(array $model): object
    {
        $this->validate($model);

        return Request::sendMessage([
            'chat_id' => $model['chat_id'],
            'text' => $model['text'],
        ]);
    }

    protected function getValidationRules(): Assert\Collection
    {
        return new Assert\Collection([
            'chat_id' => new Assert\NotBlank(),
            'text' => new Assert\NotBlank(),
        ]);
    }

}