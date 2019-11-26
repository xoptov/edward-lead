<?php

namespace AppBundle\EventListener;

use AppBundle\Event\MessageEvent;
use AppBundle\Notifications\MessageCreatedNotification;
use AppBundle\Notifications\MessageSupportReplyNotification;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MessageEventListener implements EventSubscriberInterface
{
    /**
     * @var MessageCreatedNotification
     */
    private $messageCreatedNotification;
    /**
     * @var MessageSupportReplyNotification
     */
    private $messageSupportReplyNotification;

    /**
     * MessageEventListener constructor.
     *
     * @param MessageCreatedNotification      $messageCreatedNotification
     * @param MessageSupportReplyNotification $messageSupportReplyNotification
     */
    public function __construct(
        MessageCreatedNotification $messageCreatedNotification,
        MessageSupportReplyNotification $messageSupportReplyNotification
    )
    {
        $this->messageCreatedNotification = $messageCreatedNotification;
        $this->messageSupportReplyNotification = $messageSupportReplyNotification;
    }

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
        $this->messageCreatedNotification->send($event->getMessage());
    }

    /**
     * @param MessageEvent $event
     *
     * @throws Exception
     */
    public function handleSupportReply(MessageEvent $event): void
    {
        $this->messageSupportReplyNotification->send($event->getMessage());
    }
}