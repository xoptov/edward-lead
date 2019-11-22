<?php

namespace NotificationBundle\Event;

use NotificationBundle\Model\RoomInterface;
use Symfony\Component\EventDispatcher\Event;

class RoomEvent extends Event
{
    const NEW_CREATED = 'room.new_created';
    const DEACTIVATED = 'room.deactivated';

    /**
     * @var RoomInterface
     */
    private $room;

    /**
     * @param RoomInterface $room
     */
    public function __construct(RoomInterface $room)
    {
        $this->room = $room;
    }

    /**
     * @return RoomInterface
     */
    public function getRoom(): RoomInterface
    {
        return $this->room;
    }
}
