<?php

namespace AppBundle\Notifications;

use AppBundle\Entity\Room;
use NotificationBundle\Channels\EmailChannel;

class RoomCreatedNotification implements Notification
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
     * @param Room $object
     */
    public function send(Room $object): void
    {
    }
}