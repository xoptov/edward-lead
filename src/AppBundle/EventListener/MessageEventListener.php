<?php

namespace AppBundle\EventListener;

use AppBundle\Event\MessageEvent;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MessageEventListener extends BaseEventListener implements EventSubscriberInterface
{
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            MessageEvent::NEW_CREATED => 'handleNewCreated',
            MessageEvent::SUPPORT_REPLY => 'handleSupportReply',
        ];
    }

    /**
     * @param MessageEvent $event
     *
     * @throws Exception
     */
    public function handleNewCreated(MessageEvent $event): void
    {
        $this->emailNotificationContainer->messageCreated($event->getMessage());
        $this->internalNotificationContainer->messageCreated($event->getMessage());
    }

    /**
     * @param MessageEvent $event
     *
     * @throws Exception
     */
    public function handleSupportReply(MessageEvent $event): void
    {
        $this->emailNotificationContainer->messageSupportReply($event->getMessage());
        $this->webPushNotificationContainer->messageSupportReply($event->getMessage());
    }
}