<?php

namespace AppBundle\EventListener;

use AppBundle\Event\UserEvent;
use AppBundle\Notifications\NewUserRegisteredNotification;
use AppBundle\Notifications\UserApiTokenChangedNotification;
use AppBundle\Notifications\UserPasswordChangedNotification;
use AppBundle\Notifications\UserResetTokenUpdatedNotification;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserEventListener implements EventSubscriberInterface
{
    /**
     * @var NewUserRegisteredNotification
     */
    private $newUserRegisteredNotification;
    /**
     * @var UserApiTokenChangedNotification
     */
    private $apiTokenChangedNotification;
    /**
     * @var UserPasswordChangedNotification
     */
    private $passwordChangedNotification;
    /**
     * @var UserResetTokenUpdatedNotification
     */
    private $resetTokenUpdatedNotification;

    /**
     * UserEventListener constructor.
     *
     * @param NewUserRegisteredNotification     $newUserRegisteredNotification
     * @param UserApiTokenChangedNotification   $apiTokenChangedNotification
     * @param UserPasswordChangedNotification   $passwordChangedNotification
     * @param UserResetTokenUpdatedNotification $resetTokenUpdatedNotification
     */
    public function __construct(
        NewUserRegisteredNotification $newUserRegisteredNotification,
        UserApiTokenChangedNotification $apiTokenChangedNotification,
        UserPasswordChangedNotification $passwordChangedNotification,
        UserResetTokenUpdatedNotification $resetTokenUpdatedNotification
    )
    {
        $this->newUserRegisteredNotification = $newUserRegisteredNotification;
        $this->apiTokenChangedNotification = $apiTokenChangedNotification;
        $this->passwordChangedNotification = $passwordChangedNotification;
        $this->resetTokenUpdatedNotification = $resetTokenUpdatedNotification;
    }

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
        $this->newUserRegisteredNotification->send($event->getUser());
    }

    /**
     * @param UserEvent $event
     *
     * @throws Exception
     */
    public function handleApiTokenChanged(UserEvent $event): void
    {
        $this->apiTokenChangedNotification->send($event->getUser());
    }

    /**
     * @param UserEvent $event
     *
     * @throws Exception
     */
    public function handleResetTokenUpdated(UserEvent $event): void
    {
        $this->passwordChangedNotification->send($event->getUser());
    }

    /**
     * @param UserEvent $event
     *
     * @throws Exception
     */
    public function handlePasswordChanged(UserEvent $event): void
    {
        $this->resetTokenUpdatedNotification->send($event->getUser());
    }
}