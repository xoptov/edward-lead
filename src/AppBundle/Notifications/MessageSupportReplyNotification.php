<?php

namespace AppBundle\Notifications;

use AppBundle\Entity\Message;
use Exception;
use NotificationBundle\ChannelModels\Email;
use NotificationBundle\Channels\EmailChannel;
use NotificationBundle\Constants\EsputnikEmailTemplate;

class MessageSupportReplyNotification implements Notification
{
    /**
     * @var EmailChannel
     */
    private $emailChannel;

    /**
     * OnUserRegisterNotification constructor.
     *
     * @param EmailChannel $emailChannel
     */
    public function __construct(EmailChannel $emailChannel)
    {
        $this->emailChannel = $emailChannel;
    }

    /**
     * @param Message $object
     *
     * @throws Exception
     */
    public function send(Message $object): void
    {
        $this->sendEmail($object);
    }

    /**
     * @param Message $object
     *
     * @throws Exception
     */
    private function sendEmail(Message $object): void
    {
        $email = new Email;

        $email->setToEmail($object->getSender()->getEmail());
        $email->setTemplateId($this->getEmailTemplate());
        $email->setParams(
            [
                "ID_SUPPORT" => $object->getThread()->getId(),
                "TEXT_SUPPORT" => $object->getBody(),
            ]
        );

        $this->emailChannel->send($email);
    }

    /**
     * @return string
     */
    private function getEmailTemplate(): string
    {
        return EsputnikEmailTemplate::SUPPORT_RESPONSE;
    }
}