<?php

namespace AppBundle\Notifications;

use AppBundle\Entity\Message;
use Exception;
use NotificationBundle\ChannelModels\Email;
use NotificationBundle\Channels\EmailChannel;
use NotificationBundle\Constants\EsputnikEmailTemplate;

class MessageCreatedNotification implements Notification
{
    /**
     * @var EmailChannel
     */
    private $emailChannel;

    /**
     * @var string
     */
    private $adminEmail;

    /**
     * OnUserRegisterNotification constructor.
     *
     * @param EmailChannel $emailChannel
     * @param string       $adminEmail
     */
    public function __construct(EmailChannel $emailChannel, string $adminEmail)
    {
        $this->emailChannel = $emailChannel;
        $this->adminEmail = $adminEmail;
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

        $email->setToEmail($this->adminEmail);
        $email->setTemplateId($this->getEmailTemplate());
        $email->setParams(
            [
                "ID_SUPPORT" => $object->getThread()->getId(),
                "ID_USER" => $object->getSender()->getId(),
            ]
        );

        $this->emailChannel->send($email);
    }

    /**
     * @return string
     */
    private function getEmailTemplate(): string
    {
        return EsputnikEmailTemplate::NEW_SUPPORT_CONTACT;
    }
}