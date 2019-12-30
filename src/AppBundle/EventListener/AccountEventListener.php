<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\ClientAccount;
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
        $account = $event->getAccount();

        if ($account instanceof ClientAccount) {
            $this->emailNotificationContainer->accountBalanceApproachingZero($account);
            $this->webPushNotificationContainer->accountBalanceApproachingZero($account);
            $this->smsNotificationContainer->accountBalanceApproachingZero($account);
            $this->internalNotificationContainer->accountBalanceApproachingZero($account);
        }
    }

    /**
     * @param AccountEvent $event
     *
     * @throws Exception
     */
    public function handleBalanceLowerThenMinimal(AccountEvent $event): void
    {
        $account = $event->getAccount();

        if ($account instanceof ClientAccount) {
            $this->emailNotificationContainer->accountBalanceLowerThenMinimal($account);
            $this->webPushNotificationContainer->accountBalanceApproachingZero($account);
            $this->smsNotificationContainer->accountBalanceApproachingZero($account);
            $this->internalNotificationContainer->accountBalanceLowerThenMinimal($account);
        }
    }
}