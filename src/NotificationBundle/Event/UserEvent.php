<?php

namespace NotificationBundle\Event;

use NotificationBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\Event;

class UserEvent extends Event
{
    const NEW_REGISTERED    = 'user.new_registered';
    const API_TOKEN_UPDATED = 'user.api_token_updated';

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @param UserInterface $user
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
