<?php

namespace AppBundle\Service;

use AppBundle\Entity\Room;

class RoomManager
{
    /**
     * @param Room $room
     */
    public function updateInviteToken(Room $room)
    {
        $token = md5(time() + rand(1, 1000));
        $room->setInviteToken($token);
    }
}