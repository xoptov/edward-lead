<?php

namespace AppBundle\EventListener;

use AppBundle\Notifications\EmailNotificationContainer;

abstract class BaseEventListener
{
    /**
     * @var EmailNotificationContainer
     */
    public $emailNotificationContainer;

    /**
     * BaseEventListener constructor.
     *
     * @param EmailNotificationContainer $emailNotificationContainer
     */
    public function __construct(EmailNotificationContainer $emailNotificationContainer)
    {
        $this->emailNotificationContainer = $emailNotificationContainer;
    }
}