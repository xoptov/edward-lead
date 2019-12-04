<?php

namespace AppBundle\EventListener;

use AppBundle\Event\RoomEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RoomEventListener implements EventSubscriberInterface
{
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            RoomEvent::NEW_CREATED => 'handleNewCreated',
            RoomEvent::DEACTIVATED => 'handleDeactivated',
        ];
    }

    /**
     * @param RoomEvent $event
     */
    public function handleNewCreated(RoomEvent $event): void
    {
        //
    }

    /**
     * @param RoomEvent $event
     */
    public function handleDeactivated(RoomEvent $event): void
    {
        //
    }
}