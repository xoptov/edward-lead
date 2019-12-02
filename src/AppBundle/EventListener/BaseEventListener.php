<?php

namespace AppBundle\EventListener;

use AppBundle\Notifications\EmailNotificationContainer;
use AppBundle\Notifications\SmsNotificationContainer;
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
     * @var SmsNotificationContainer
     */
    public $smsNotificationContainer;

    /**
     * BaseEventListener constructor.
     *
     * @param EmailNotificationContainer   $emailNotificationContainer
     * @param WebPushNotificationContainer $webPushNotificationContainer
     * @param SmsNotificationContainer     $smsNotificationContainer
     */
    public function __construct(
        EmailNotificationContainer $emailNotificationContainer,
        WebPushNotificationContainer $webPushNotificationContainer,
        SmsNotificationContainer $smsNotificationContainer
    )
    {
        $this->emailNotificationContainer = $emailNotificationContainer;
        $this->webPushNotificationContainer = $webPushNotificationContainer;
        $this->smsNotificationContainer = $smsNotificationContainer;
    }

}