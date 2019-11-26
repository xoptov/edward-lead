<?php

namespace AppBundle\Notifications;

use AppBundle\Entity\Trade;
use NotificationBundle\Channels\EmailChannel;

class TradeRejectNotification implements Notification
{
    /**
     * @var EmailChannel
     */
    private $emailChannel;

    /**
     * OnUserRegisterNotification constructor.
     *
     * @param EmailChannel $emailChannel
     */
    public function __construct(EmailChannel $emailChannel)
    {
        $this->emailChannel = $emailChannel;
    }

    /**
     * @param Trade $object
     */
    public function send(Trade $object): void
    {
    }
}