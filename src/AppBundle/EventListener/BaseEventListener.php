<?php

namespace AppBundle\EventListener;

use AppBundle\Notifications\EmailNotificationContainer;

use AppBundle\Notifications\InternalNotificationContainer;
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
     * @var InternalNotificationContainer
     */
    public $internalNotificationContainer;


    /**
     * BaseEventListener constructor.
     *
     * @param EmailNotificationContainer    $emailNotificationContainer
     * @param SmsNotificationContainer      $smsNotificationContainer
     * @param InternalNotificationContainer $internalNotificationContainer
     */
    public function __construct(
        EmailNotificationContainer $emailNotificationContainer,
        SmsNotificationContainer $smsNotificationContainer,
        InternalNotificationContainer $internalNotificationContainer
    )
    {
        $this->emailNotificationContainer = $emailNotificationContainer;
        $this->smsNotificationContainer = $smsNotificationContainer;
        $this->internalNotificationContainer = $internalNotificationContainer;
    }
}