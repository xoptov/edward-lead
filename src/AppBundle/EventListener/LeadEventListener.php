<?php

namespace AppBundle\EventListener;

use AppBundle\Event\LeadEvent;
use AppBundle\Notifications\LeadExpectTooLongNotification;
use AppBundle\Notifications\LeadInWorkTooLongNotification;
use AppBundle\Notifications\LeadNewPlacedNotification;
use AppBundle\Notifications\LeadNoVisitTooLongNotification;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LeadEventListener implements EventSubscriberInterface
{
    /**
     * @var LeadExpectTooLongNotification
     */
    private $expectTooLongNotification;
    /**
     * @var LeadInWorkTooLongNotification
     */
    private $leadInWorkTooLongNotification;
    /**
     * @var LeadNewPlacedNotification
     */
    private $leadNewPlacedNotification;
    /**
     * @var LeadNoVisitTooLongNotification
     */
    private $leadNoVisitTooLongNotification;

    /**
     * LeadEventListener constructor.
     *
     * @param LeadExpectTooLongNotification  $expectTooLongNotification
     * @param LeadInWorkTooLongNotification  $leadInWorkTooLongNotification
     * @param LeadNewPlacedNotification      $leadNewPlacedNotification
     * @param LeadNoVisitTooLongNotification $leadNoVisitTooLongNotification
     */
    public function __construct(
        LeadExpectTooLongNotification $expectTooLongNotification,
        LeadInWorkTooLongNotification $leadInWorkTooLongNotification,
        LeadNewPlacedNotification $leadNewPlacedNotification,
        LeadNoVisitTooLongNotification $leadNoVisitTooLongNotification
    )
    {
        $this->expectTooLongNotification = $expectTooLongNotification;
        $this->leadInWorkTooLongNotification = $leadInWorkTooLongNotification;
        $this->leadNewPlacedNotification = $leadNewPlacedNotification;
        $this->leadNoVisitTooLongNotification = $leadNoVisitTooLongNotification;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            LeadEvent::NEW_PLACED => 'handleNewPlaced',
            LeadEvent::NO_VISIT_TOO_LONG => 'handleNoVisitTooLong',
            LeadEvent::EXPECT_TOO_LONG => 'handleExpectTooLong',
            LeadEvent::IN_WORK_TOO_LONG => 'handleInWorkTooLong',
        ];
    }

    /**
     * @param LeadEvent $event
     *
     * @throws Exception
     */
    public function handleNewPlaced(LeadEvent $event): void
    {
        $this->leadNewPlacedNotification->send($event->getLead());
    }

    /**
     * @param LeadEvent $event
     *
     * @throws Exception
     */
    public function handleNoVisitTooLong(LeadEvent $event): void
    {
        $this->leadNoVisitTooLongNotification->send($event->getLead());
    }

    /**
     * @param LeadEvent $event
     */
    public function handleExpectTooLong(LeadEvent $event): void
    {
        $this->expectTooLongNotification->send($event->getLead());
    }

    /**
     * @param LeadEvent $event
     */
    public function handleInWorkTooLong(LeadEvent $event): void
    {
        $this->leadInWorkTooLongNotification->send($event->getLead());
    }

}