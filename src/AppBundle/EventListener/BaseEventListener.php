<?php

namespace AppBundle\EventListener;

use AppBundle\Notifications\EmailNotificationContainer;
use AppBundle\Notifications\SmsNotificationContainer;

abstract class BaseEventListener
{
    /**
     * @var EmailNotificationContainer
     */
    public $emailNotificationContainer;
    /**
     * @var SmsNotificationContainer
     */
    public $smsNotificationContainer;

    /**
     * BaseEventListener constructor.
     *
     * @param EmailNotificationContainer $emailNotificationContainer
     * @param SmsNotificationContainer   $smsNotificationContainer
     */
    public function __construct(
        EmailNotificationContainer $emailNotificationContainer,
        SmsNotificationContainer $smsNotificationContainer
    )
    {
        $this->emailNotificationContainer = $emailNotificationContainer;
        $this->smsNotificationContainer = $smsNotificationContainer;
    }
}