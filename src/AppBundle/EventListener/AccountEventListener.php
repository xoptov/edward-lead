<?php

namespace AppBundle\EventListener;

use AppBundle\Event\AccountEvent;
use AppBundle\Notifications\AccountBalanceApproachingZeroNotification;
use AppBundle\Notifications\AccountBalanceLowerThenMinimalNotification;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AccountEventListener implements EventSubscriberInterface
{
    /**
     * @var AccountBalanceApproachingZeroNotification
     */
    private $accountBalanceApproachingZeroNotification;
    /**
     * @var AccountBalanceLowerThenMinimalNotification
     */
    private $accountBalanceLowerThenMinimalNotification;

    /**
     * AccountEventListener constructor.
     *
     * @param AccountBalanceApproachingZeroNotification  $accountBalanceApproachingZeroNotification
     * @param AccountBalanceLowerThenMinimalNotification $accountBalanceLowerThenMinimalNotification
     */
    public function __construct(
        AccountBalanceApproachingZeroNotification $accountBalanceApproachingZeroNotification,
        AccountBalanceLowerThenMinimalNotification $accountBalanceLowerThenMinimalNotification
    )
    {
        $this->accountBalanceApproachingZeroNotification = $accountBalanceApproachingZeroNotification;
        $this->accountBalanceLowerThenMinimalNotification = $accountBalanceLowerThenMinimalNotification;
    }

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
        $this->accountBalanceApproachingZeroNotification->send($event->getAccount());

    }

    /**
     * @param AccountEvent $event
     *
     * @throws Exception
     */
    public function handleBalanceLowerThenMinimal(AccountEvent $event): void
    {
        $this->accountBalanceLowerThenMinimalNotification->send($event->getAccount());
    }
}