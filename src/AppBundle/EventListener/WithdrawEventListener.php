<?php

namespace AppBundle\EventListener;

use AppBundle\Event\WithdrawEvent;
use AppBundle\Notifications\WithdrawAcceptedNotification;
use AppBundle\Notifications\WithdrawAdminNotification;
use AppBundle\Notifications\WithdrawRejectedNotification;
use AppBundle\Notifications\WithdrawUserNotification;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WithdrawEventListener implements EventSubscriberInterface
{
    /**
     * @var WithdrawAcceptedNotification
     */
    private $withdrawAcceptedNotification;
    /**
     * @var WithdrawAdminNotification
     */
    private $withdrawAdminNotification;
    /**
     * @var WithdrawRejectedNotification
     */
    private $withdrawRejectedNotification;
    /**
     * @var WithdrawUserNotification
     */
    private $withdrawUserNotification;

    /**
     * WithdrawEventListener constructor.
     *
     * @param WithdrawAcceptedNotification $withdrawAcceptedNotification
     * @param WithdrawAdminNotification    $withdrawAdminNotification
     * @param WithdrawRejectedNotification $withdrawRejectedNotification
     * @param WithdrawUserNotification     $withdrawUserNotification
     */
    public function __construct(
        WithdrawAcceptedNotification $withdrawAcceptedNotification,
        WithdrawAdminNotification $withdrawAdminNotification,
        WithdrawRejectedNotification $withdrawRejectedNotification,
        WithdrawUserNotification $withdrawUserNotification
    )
    {
        $this->withdrawAcceptedNotification = $withdrawAcceptedNotification;
        $this->withdrawAdminNotification = $withdrawAdminNotification;
        $this->withdrawRejectedNotification = $withdrawRejectedNotification;
        $this->withdrawUserNotification = $withdrawUserNotification;
    }

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
        $this->withdrawUserNotification->send($event->getWithdraw());
        $this->withdrawAdminNotification->send($event->getWithdraw());
    }

    /**
     * @param WithdrawEvent $event
     *
     * @throws Exception
     */
    public function handleAccepted(WithdrawEvent $event): void
    {
        $this->withdrawAcceptedNotification->send($event->getWithdraw());
    }

    /**
     * @param WithdrawEvent $event
     *
     * @throws Exception
     */
    public function handleRejected(WithdrawEvent $event): void
    {
        $this->withdrawRejectedNotification->send($event->getWithdraw());
    }
}