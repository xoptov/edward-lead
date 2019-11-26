<?php

namespace AppBundle\EventListener;

use AppBundle\Event\MemberEvent;
use AppBundle\Notifications\MemberJoinedToYouNotification;
use AppBundle\Notifications\MemberRemovedNotification;
use AppBundle\Notifications\SomeMemberJoinedNotification;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MemberEventListener implements EventSubscriberInterface
{
    /**
     * @var MemberJoinedToYouNotification
     */
    private $memberJoinedToYouNotification;
    /**
     * @var MemberRemovedNotification
     */
    private $memberRemovedNotification;
    /**
     * @var SomeMemberJoinedNotification
     */
    private $someMemberJoinedNotification;

    /**
     * MemberEventListener constructor.
     *
     * @param MemberJoinedToYouNotification $memberJoinedToYouNotification
     * @param MemberRemovedNotification     $memberRemovedNotification
     * @param SomeMemberJoinedNotification  $someMemberJoinedNotification
     */
    public function __construct(
        MemberJoinedToYouNotification $memberJoinedToYouNotification,
        MemberRemovedNotification $memberRemovedNotification,
        SomeMemberJoinedNotification $someMemberJoinedNotification
    )
    {
        $this->memberJoinedToYouNotification = $memberJoinedToYouNotification;
        $this->memberRemovedNotification = $memberRemovedNotification;
        $this->someMemberJoinedNotification = $someMemberJoinedNotification;
    }

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
        $this->memberJoinedToYouNotification->send($event->getMember());
    }

    /**
     * @param MemberEvent $event
     */
    public function handleRemoved(MemberEvent $event): void
    {
        $this->memberRemovedNotification->send($event->getMember());
    }
}