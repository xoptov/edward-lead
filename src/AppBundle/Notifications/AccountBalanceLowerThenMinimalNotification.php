<?php

namespace AppBundle\Notifications;

use AppBundle\Entity\ClientAccount;
use Exception;
use NotificationBundle\ChannelModels\Email;
use NotificationBundle\Channels\EmailChannel;
use NotificationBundle\Constants\EsputnikEmailTemplate;

class AccountBalanceLowerThenMinimalNotification implements Notification
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
     * @param ClientAccount $object
     *
     * @throws Exception
     */
    public function send(ClientAccount $object): void
    {
        $this->sendEmail($object);
    }

    /**
     * @param ClientAccount $object
     *
     * @throws Exception
     */
    private function sendEmail(ClientAccount $object): void
    {
        $email = new Email;

        $email->setToEmail($object->getUser()->getEmail());
        $email->setTemplateId($this->getEmailTemplate());
        $email->setParams(
            [
                "NAME" => $object->getUser()->getName(),
            ]
        );

        $this->emailChannel->send($email);
    }

    /**
     * @return string
     */
    private function getEmailTemplate(): string
    {
        return EsputnikEmailTemplate::BALANCE_LOWER_THEN_MINIMAL;
    }
}