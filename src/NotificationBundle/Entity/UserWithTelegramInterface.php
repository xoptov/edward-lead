<?php

namespace NotificationBundle\Entity;

interface UserWithTelegramInterface
{
    /**
     * @return string
     */
    public function getTelegramChatId(): string;

    /**
     * @param string $telegramChatId
     */
    public function setTelegramChatId(string $telegramChatId): void;

    /**
     * @return string
     */
    public function getTelegramAuthToken(): string;

    /**
     * @param string $telegramAuthToken
     */
    public function setTelegramAuthToken(string $telegramAuthToken): void;

}