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
    private $user;

    /**
     * ConfigureTelegramEvent constructor.
     * @param UserWithTelegramInterface $userWithTelegram
     */
    public function __construct(UserWithTelegramInterface $userWithTelegram)
    {
        $this->user = $userWithTelegram;
    }

    /**
     * @return UserWithTelegramInterface
     */
    public function getUser(): UserWithTelegramInterface
    {
        return $this->user;
    }

    /**
     * @param UserWithTelegramInterface $user
     */
    public function setUser(UserWithTelegramInterface $user): void
    {
        $this->user = $user;
    }
}