<?php

namespace NotificationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

interface UserNotificationInterface
{
    /**
     * @return mixed
     */
    public function getNotifications();

    /**
     * @param ArrayCollection $notifications
     * @return void
     */
    public function setNotifications(ArrayCollection $notifications): void;
}