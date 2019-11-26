<?php

namespace AppBundle\Notifications;

use AppBundle\Entity\Lead;
use Exception;
use NotificationBundle\ChannelModels\Email;
use NotificationBundle\Channels\EmailChannel;
use NotificationBundle\Constants\EsputnikEmailTemplate;

class LeadNoVisitTooLongNotification implements Notification
{
    /**
     * @var EmailChannel
     */
    private $emailChannel;

    /**
     * RoomCreatedNotification constructor.
     *
     * @param EmailChannel $emailChannel
     */
    public function __construct(EmailChannel $emailChannel)
    {
        $this->emailChannel = $emailChannel;
    }

    /**
     * @param Lead $object
     *
     * @throws Exception
     */
    public function send(Lead $object): void
    {
        $this->sendEmail($object);
    }

    /**
     * @param Lead $object
     *
     * @throws Exception
     */
    private function sendEmail(Lead $object): void
    {
        $email = new Email;

        $email->setToEmail($object->getUser()->getEmail());
        $email->setTemplateId($this->getEmailTemplate());

        $this->emailChannel->send($email);
    }

    /**
     * @return string
     */
    private function getEmailTemplate(): string
    {
        return EsputnikEmailTemplate::NO_VISITING_FOR_TOO_LONG;
    }
}