<?php

namespace NotificationBundle\Model;

interface UserInterface
{
    /**
     * @return string|null
     */
    public function getName(): ?string;
}