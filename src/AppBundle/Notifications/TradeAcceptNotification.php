<?php

namespace AppBundle\Notifications;

use AppBundle\Entity\Trade;
use NotificationBundle\Channels\EmailChannel;

class TradeAcceptNotification implements Notification
{
    /**
     * @var EmailChannel
     */
    private $emailChannel;

    /**
     * TradeAcceptNotification constructor.
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