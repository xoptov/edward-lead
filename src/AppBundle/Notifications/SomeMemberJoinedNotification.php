<?php

namespace AppBundle\Notifications;

use AppBundle\Entity\Member;
use NotificationBundle\Channels\EmailChannel;

class SomeMemberJoinedNotification implements Notification
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
     * @param Member $object
     */
    public function send(Member $object): void
    {
    }
}