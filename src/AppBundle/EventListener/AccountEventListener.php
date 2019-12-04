<?php

namespace AppBundle\EventListener;

use AppBundle\Event\AccountEvent;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AccountEventListener extends BaseEventListener implements EventSubscriberInterface
{
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            AccountEvent::BALANCE_APPROACHING_ZERO => 'handleBalanceApproachingZero',
            AccountEvent::BALANCE_LOWER_THEN_MINIMAL => 'handleBalanceLowerThenMinimal',
        ];
    }

    /**
     * @param AccountEvent $event
     *
     * @throws Exception
     */
    public function handleBalanceApproachingZero(AccountEvent $event): void
    {
        $this->emailNotificationContainer->accountBalanceApproachingZero($event->getAccount());
        $this->smsNotificationContainer->accountBalanceApproachingZero($event->getAccount());
        $this->internalNotificationContainer->accountBalanceApproachingZero($event->getAccount());
    }

    /**
     * @param AccountEvent $event
     *
     * @throws Exception
     */
    public function handleBalanceLowerThenMinimal(AccountEvent $event): void
    {
        $this->emailNotificationContainer->accountBalanceLowerThenMinimal($event->getAccount());
        $this->smsNotificationContainer->accountBalanceApproachingZero($event->getAccount());
        $this->internalNotificationContainer->accountBalanceLowerThenMinimal($event->getAccount());
    }
}