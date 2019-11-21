<?php

namespace NotificationBundle\Event;

use NotificationBundle\Entity\UserWithTelegramInterface;
use Symfony\Component\EventDispatcher\Event;

class ConfigureTelegramEvent extends Event
{
    const NAME = 'notification.configure_telegram';

    /**
     * @var UserWithTelegramInterface
     */
    private $userWithTelegram;

    /**
     * ConfigureTelegramEvent constructor.
     * @param UserWithTelegramInterface $userWithTelegram
     */
    public function __construct(UserWithTelegramInterface $userWithTelegram)
    {
        $this->userWithTelegram = $userWithTelegram;
    }

    /**
     * @return UserWithTelegramInterface
     */
    public function getUserWithTelegram(): UserWithTelegramInterface
    {
        return $this->userWithTelegram;
    }

    /**
     * @param UserWithTelegramInterface $userWithTelegram
     */
    public function setUserWithTelegram(UserWithTelegramInterface $userWithTelegram): void
    {
        $this->userWithTelegram = $userWithTelegram;
    }
}