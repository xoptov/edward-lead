<?php

namespace NotificationBundle\Model;

interface RoomInterface
{
    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return string|null
     */
    public function getName(): ?string;
}
