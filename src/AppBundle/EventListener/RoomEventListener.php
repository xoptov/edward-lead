<?php

namespace AppBundle\EventListener;

use AppBundle\Event\RoomEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RoomEventListener extends BaseEventListener implements EventSubscriberInterface
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
        $this->internalNotificationContainer->newRoomCreated($event->getRoom());
    }

    /**
     * @param RoomEvent $event
     */
    public function handleDeactivated(RoomEvent $event): void
    {
        $this->internalNotificationContainer->roomDeactivated($event->getRoom());
    }
}