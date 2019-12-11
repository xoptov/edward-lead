<?php

namespace AppBundle\EventListener;

use AppBundle\Notifications\EmailNotificationContainer;
use AppBundle\Notifications\InternalNotificationContainer;
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
     * @var InternalNotificationContainer
     */
    public $internalNotificationContainer;

    /**
     * BaseEventListener constructor.
     *
     * @param EmailNotificationContainer    $emailNotificationContainer
     * @param WebPushNotificationContainer  $webPushNotificationContainer
     * @param SmsNotificationContainer      $smsNotificationContainer
     * @param InternalNotificationContainer $internalNotificationContainer
     */
    public function __construct(
        EmailNotificationContainer $emailNotificationContainer,
        WebPushNotificationContainer $webPushNotificationContainer,
        SmsNotificationContainer $smsNotificationContainer,
        InternalNotificationContainer $internalNotificationContainer

    )
    {
        $this->emailNotificationContainer = $emailNotificationContainer;
        $this->webPushNotificationContainer = $webPushNotificationContainer;
        $this->smsNotificationContainer = $smsNotificationContainer;
        $this->internalNotificationContainer = $internalNotificationContainer;
    }

}