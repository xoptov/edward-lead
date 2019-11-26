<?php

namespace AppBundle\Notifications;

use AppBundle\Entity\Withdraw;
use Exception;
use NotificationBundle\ChannelModels\Email;
use NotificationBundle\Channels\EmailChannel;
use NotificationBundle\Constants\EsputnikEmailTemplate;

class WithdrawUserNotification implements Notification
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
     * @param Withdraw $object
     *
     * @throws Exception
     */
    public function send(Withdraw $object): void
    {
        $this->sendEmail($object);
    }

    /**
     * @param Withdraw $object
     *
     * @throws Exception
     */
    private function sendEmail(Withdraw $object): void
    {
        $email = new Email;

        $email->setToEmail($object->getUser()->getEmail());
        $email->setTemplateId($this->getEmailTemplate());
        $email->setParams(
            [
                "BALANCE_LOGOUT" => $object->getAmount(),
                "ID_BALANCE_LOGOUT" => $object->getId(),
            ]
        );

        $this->emailChannel->send($email);
    }

    /**
     * @return string
     */
    private function getEmailTemplate(): string
    {
        return EsputnikEmailTemplate::BALANCE_WITHDRAW_FOR_USER;
    }
}