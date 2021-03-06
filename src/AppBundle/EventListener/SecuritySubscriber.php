<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\User;
use AppBundle\Entity\ClientAccount;
use AppBundle\Entity\HistoryAction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecuritySubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin'
        ];
    }

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $token = $event->getAuthenticationToken();
        $user = $token->getUser();

        if ($user instanceof User) {
            $historyAction = new HistoryAction();
            $historyAction->setUser($user);
            $this->entityManager->persist($historyAction);

            if (!$user->getAccount()) {
                $account = new ClientAccount();
                $account->setUser($user);
                $this->entityManager->persist($account);
            }

            $this->entityManager->flush();
        }
    }
}