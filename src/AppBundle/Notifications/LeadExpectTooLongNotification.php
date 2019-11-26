<?php

namespace AppBundle\Notifications;

use AppBundle\Entity\Lead;
use NotificationBundle\Channels\EmailChannel;

class LeadExpectTooLongNotification implements Notification
{
    /**
     * @var EmailChannel
     */
    private $emailChannel;

    /**
     * RoomCreatedNotification constructor.
     *
     * @param EmailChannel $emailChannel
     */
    public function __construct(EmailChannel $emailChannel)
    {
        $this->emailChannel = $emailChannel;
    }

    /**
     * @param Lead $object
     */
    public function send(Lead $object): void
    {
    }
}