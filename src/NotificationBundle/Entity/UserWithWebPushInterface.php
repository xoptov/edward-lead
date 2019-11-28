<?php

namespace NotificationBundle\Entity;

interface UserWithWebPushInterface
{
    /**
     * @return string
     */
    public function getWebPushToken(): string;

    /**
     * @param string $webPushToken
     */
    public function setWebPushToken(string $webPushToken): void;
}