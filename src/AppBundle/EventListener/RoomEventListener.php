<?php

namespace AppBundle\EventListener;

use AppBundle\Event\RoomEvent;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use NotificationBundle\Exception\ValidationNotificationClientException;
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
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ValidationNotificationClientException
     */
    public function handleNewCreated(RoomEvent $event): void
    {
        $this->internalNotificationContainer->newRoomCreated($event->getRoom());
    }

    /**
     * @param RoomEvent $event
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ValidationNotificationClientException
     */
    public function handleDeactivated(RoomEvent $event): void
    {
        $this->internalNotificationContainer->roomDeactivated($event->getRoom());
    }
}