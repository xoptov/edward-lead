<?php

namespace AppBundle\EventListener;

use AppBundle\Event\UserEvent;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserEventListener extends BaseEventListener implements EventSubscriberInterface
{
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            UserEvent::NEW_REGISTERED => 'handleNewRegistered',
            UserEvent::API_TOKEN_CHANGED => 'handleApiTokenChanged',
            UserEvent::RESET_TOKEN_UPDATED => 'handleResetTokenUpdated',
            UserEvent::PASSWORD_CHANGED => 'handlePasswordChanged',
        ];
    }

    /**
     * @param UserEvent $event
     *
     * @throws Exception
     */
    public function handleNewRegistered(UserEvent $event): void
    {
        $this->emailNotificationContainer->newUserRegistered($event->getUser());
    }

    /**
     * @param UserEvent $event
     *
     * @throws Exception
     */
    public function handleApiTokenChanged(UserEvent $event): void
    {
        $this->emailNotificationContainer->userApiTokenChanged($event->getUser());
    }

    /**
     * @param UserEvent $event
     *
     * @throws Exception
     */
    public function handleResetTokenUpdated(UserEvent $event): void
    {
        $this->emailNotificationContainer->userResetTokenUpdated($event->getUser());
    }

    /**
     * @param UserEvent $event
     *
     * @throws Exception
     */
    public function handlePasswordChanged(UserEvent $event): void
    {
        $this->emailNotificationContainer->userPasswordChanged($event->getUser());
    }
}