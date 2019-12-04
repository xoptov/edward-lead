<?php

namespace AppBundle\EventListener;

use AppBundle\Event\MemberEvent;
use Exception;
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
     */
    public function handleJoined(MemberEvent $event): void
    {
        //
    }

    /**
     * @param MemberEvent $event
     */
    public function handleRemoved(MemberEvent $event): void
    {
        //
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