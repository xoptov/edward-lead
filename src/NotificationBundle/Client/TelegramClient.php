<?php

namespace NotificationBundle\Client;

use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use NotificationBundle\Exception\ValidationNotificationClientException;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;

class TelegramClient extends Client
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

    /**
     * @return Collection
     */
    protected function getValidationRules(): Collection
    {
        return new Collection([
            'chat_id' => new NotBlank(),
            'text' => new NotBlank(),
        ]);
    }

}