<?php

namespace AppBundle\EventListener;

use AppBundle\Event\RoomEvent;
use AppBundle\Notifications\RoomCreatedNotification;
use AppBundle\Notifications\RoomDeactivatedNotification;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RoomEventListener implements EventSubscriberInterface
{
    /**
     * @var RoomCreatedNotification
     */
    private $roomCreatedNotification;
    /**
     * @var RoomDeactivatedNotification
     */
    private $roomDeactivatedNotification;

    public function __construct(
        RoomCreatedNotification $roomCreatedNotification,
        RoomDeactivatedNotification $roomDeactivatedNotification
    )
    {
        $this->roomCreatedNotification = $roomCreatedNotification;
        $this->roomDeactivatedNotification = $roomDeactivatedNotification;
    }

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
        $this->roomCreatedNotification->send($event->getRoom());
    }

    /**
     * @param RoomEvent $event
     */
    public function handleDeactivated(RoomEvent $event): void
    {
        $this->roomDeactivatedNotification->send($event->getRoom());
    }
}