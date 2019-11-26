<?php

namespace AppBundle\Notifications;

use AppBundle\Entity\User;
use Exception;
use NotificationBundle\ChannelModels\Email;
use NotificationBundle\Channels\EmailChannel;
use NotificationBundle\Constants\EsputnikEmailTemplate;

class UserPasswordChangedNotification implements Notification
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
     * @param User $object
     *
     * @throws Exception
     */
    public function send(User $object): void
    {
        $this->sendEmail($object);
    }

    /**
     * @param User $object
     *
     * @throws Exception
     */
    private function sendEmail(User $object): void
    {
        $email = new Email;

        $email->setToEmail($object->getEmail());
        $email->setTemplateId($this->getEmailTemplate());

        $this->emailChannel->send($email);
    }

    /**
     * @return string
     */
    private function getEmailTemplate(): string
    {
        return EsputnikEmailTemplate::PASSWORD_CHANGE_SUCCESS;
    }
}