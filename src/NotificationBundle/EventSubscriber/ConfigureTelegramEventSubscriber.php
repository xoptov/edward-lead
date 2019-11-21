<?php

namespace NotificationBundle\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use NotificationBundle\Event\ConfigureTelegramEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigureTelegramEventSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ConfigureTelegramEvent::NAME => 'setTelegramAuthToken'
        ];
    }

    /**
     * @param ConfigureTelegramEvent $event
     * @throws Exception
     */
    public function setTelegramAuthToken(ConfigureTelegramEvent $event): void
    {
        $user = $event->getUserWithTelegram();
        $randomString = bin2hex(random_bytes(10));
        $user->setTelegramAuthToken($randomString);
    }
}