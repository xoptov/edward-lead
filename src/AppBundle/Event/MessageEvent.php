<?php

namespace AppBundle\Event;

use AppBundle\Entity\Message;
use Symfony\Component\EventDispatcher\Event;

class MessageEvent extends Event
{
    const NEW_CREATED   = 'message.new_created';
    const SUPPORT_REPLY = 'message.support_reply';

    /**
     * @var Message
     */
    private $message;

    /**
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }
}