<?php

namespace AppBundle\EventListener;

use AppBundle\Event\UserEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [UserEvent::NEW_USER_REGISTERED => 'onRegistered'];
    }

    /**
     * @param UserEvent $event
     */
    public function onRegistered(UserEvent $event)
    {
        $user = $event->getUser();
        $user->setEnabled(true);

        $this->entityManager->flush();
    }
}