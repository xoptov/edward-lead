<?php

namespace NotificationBundle\Notifications;

use AppBundle\Entity\User;
use NotificationBundle\ChannelModels\Email;
use NotificationBundle\Channels\EmailChannel;

class OnUserRegisterNotification implements Notification
{
    /**
     * @var EmailChannel
     */
    private $emailChannel;
    /**
     * @var string
     */
    private $emailTemplateId;

    /**
     * OnUserRegisterNotification constructor.
     * @param EmailChannel $emailChannel
     * @param $emailTemplateId
     */
    public function __construct(EmailChannel $emailChannel, string $emailTemplateId)
    {
        $this->emailChannel = $emailChannel;
        $this->emailTemplateId = $emailTemplateId;
    }

    public function send(User $user): void
    {
        $this->sendEmail($user);
    }

    private function sendEmail(User $user)
    {
        $email = new Email;

        $email->setToEmail($user->getEmail());
        $email->setTemplateId($this->emailTemplateId);
        $email->setParams(["name" => $user->getName()]);

        $this->emailChannel->send($email);
    }
}