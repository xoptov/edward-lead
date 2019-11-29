<?php

namespace AppBundle\EventListener;

use AppBundle\Notifications\EmailNotificationContainer;
use AppBundle\Notifications\WebPushNotificationContainer;

abstract class BaseEventListener
{
    /**
     * @var EmailNotificationContainer
     */
    public $emailNotificationContainer;

    /**
     * @var WebPushNotificationContainer
     */
    public $webPushNotificationContainer;

    /**
     * BaseEventListener constructor.
     *
     * @param EmailNotificationContainer   $emailNotificationContainer
     * @param WebPushNotificationContainer $webPushNotificationContainer
     */
    public function __construct(
        EmailNotificationContainer $emailNotificationContainer,
        WebPushNotificationContainer $webPushNotificationContainer
    )
    {
        $this->emailNotificationContainer = $emailNotificationContainer;
        $this->webPushNotificationContainer = $webPushNotificationContainer;
    }
}