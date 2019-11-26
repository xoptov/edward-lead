<?php

namespace AppBundle\EventListener;

use AppBundle\Event\LeadEvent;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LeadEventListener extends BaseEventListener implements EventSubscriberInterface
{
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
        $this->emailNotificationContainer->leadNewPlaced($event->getLead());
    }

    /**
     * @param LeadEvent $event
     *
     * @throws Exception
     */
    public function handleNoVisitTooLong(LeadEvent $event): void
    {
        $this->emailNotificationContainer->noVisitTooLong($event->getLead());
    }

    /**
     * @param LeadEvent $event
     */
    public function handleExpectTooLong(LeadEvent $event): void
    {
        //
    }

    /**
     * @param LeadEvent $event
     */
    public function handleInWorkTooLong(LeadEvent $event): void
    {
        //
    }

}