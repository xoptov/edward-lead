<?php

namespace AppBundle\EventListener;

use AppBundle\Event\WithdrawEvent;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WithdrawEventListener extends BaseEventListener implements EventSubscriberInterface
{
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            WithdrawEvent::NEW_CREATED => 'handleNewCreated',
            WithdrawEvent::ACCEPTED => 'handleAccepted',
            WithdrawEvent::REJECTED => 'handleRejected',
        ];
    }

    /**
     * @param WithdrawEvent $event
     *
     * @throws Exception
     */
    public function handleNewCreated(WithdrawEvent $event): void
    {
        $this->emailNotificationContainer->withdrawUser($event->getWithdraw());
        $this->smsNotificationContainer->withdrawUser($event->getWithdraw());
        $this->emailNotificationContainer->withdrawAdmin($event->getWithdraw());
    }

    /**
     * @param WithdrawEvent $event
     *
     * @throws Exception
     */
    public function handleAccepted(WithdrawEvent $event): void
    {
        $this->emailNotificationContainer->withdrawAccepted($event->getWithdraw());
    }

    /**
     * @param WithdrawEvent $event
     *
     * @throws Exception
     */
    public function handleRejected(WithdrawEvent $event): void
    {
        $this->emailNotificationContainer->withdrawRejected($event->getWithdraw());
    }
}