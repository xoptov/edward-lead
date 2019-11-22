<?php

namespace NotificationBundle\Model;

interface MemberInterface
{
    /**
     * @return RoomInterface
     */
    public function getRoom(): RoomInterface;

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface;
}
