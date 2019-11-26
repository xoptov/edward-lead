<?php

namespace AppBundle\EventListener;

use AppBundle\Event\MemberEvent;
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
}