<?php

namespace AppBundle\EventListener;

use AppBundle\Event\MemberEvent;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use NotificationBundle\Exception\ValidationNotificationClientException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MemberEventListener extends BaseEventListener implements EventSubscriberInterface
{
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            MemberEvent::JOINED => 'handleJoined',
            MemberEvent::REMOVED => 'handleRemoved',
            MemberEvent::NO_VISIT_TOO_LONG => 'handleNoVisitTooLong',
        ];
    }

    /**
     * @param MemberEvent $event
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ValidationNotificationClientException
     */
    public function handleJoined(MemberEvent $event): void
    {
        $this->internalNotificationContainer->someOneJoinedToYou($event->getMember());
        $this->internalNotificationContainer->youJoinedToRoom($event->getMember());
    }

    /**
     * @param MemberEvent $event
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ValidationNotificationClientException
     */
    public function handleRemoved(MemberEvent $event): void
    {
        $this->internalNotificationContainer->youRemovedFromRoom($event->getMember());
    }

    /**
     * @param MemberEvent $event
     *
     * @throws Exception
     */
    public function handleNoVisitTooLong(MemberEvent $event): void
    {
        $this->emailNotificationContainer->noVisitTooLong($event->getMember());
    }
}