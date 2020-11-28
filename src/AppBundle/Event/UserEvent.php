<?php

namespace AppBundle\Event;

use AppBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class UserEvent extends Event
{
    const NEW_REGISTERED      = 'user.new_registered';
    const RESET_TOKEN_UPDATED = 'user.reset_token_updated';
    const API_TOKEN_CHANGED   = 'user.api_token_changed';
    const PASSWORD_CHANGED    = 'user.password_changed';
    const PASSWORD_RESET      = 'user.password_reset';
    const UPDATED             = 'user.updated';
    const PERSONAL_CREATED    = 'user.personal_created';
    const PERSONAL_UPDATED    = 'user.personal_updated';

    /**
     * @var User
     */
    private $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}