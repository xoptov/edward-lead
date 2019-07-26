<?php

namespace AppBundle\Event;

use AppBundle\Entity\Room;
use Symfony\Component\EventDispatcher\Event;

class RoomEvent extends Event
{
    const DEACTIVATED = 'room.deactivated';

    /**
     * @var Room
     */
    private $room;

    /**
     * @param Room $room
     */
    public function __construct(Room $room)
    {
        $this->room = $room;
    }

    /**
     * @return Room
     */
    public function getRoom(): Room
    {
        return $this->room;
    }
}