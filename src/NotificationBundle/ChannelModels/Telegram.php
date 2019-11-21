<?php

namespace NotificationBundle\ChannelModels;

use Symfony\Component\Validator\Constraints as Assert;

class Telegram implements ChannelInterface
{
    /**
     * @Assert\NotBlank
     * @var string
     */
    public $chatId;

    /**
     * @Assert\NotBlank
     * @var string
     */
    public $message;

    /**
     * @return string
     */
    public function getChatId(): string
    {
        return $this->chatId;
    }

    /**
     * @param string $chatId
     */
    public function setChatId(string $chatId): void
    {
        $this->chatId = $chatId;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

}