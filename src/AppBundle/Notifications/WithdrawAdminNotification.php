<?php

namespace AppBundle\Notifications;

use AppBundle\Entity\Withdraw;
use Exception;
use NotificationBundle\ChannelModels\Email;
use NotificationBundle\Channels\EmailChannel;
use NotificationBundle\Constants\EsputnikEmailTemplate;

class WithdrawAdminNotification implements Notification
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
     * RoomCreatedNotification constructor.
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

        $email->setToEmail($this->adminEmail);
        $email->setTemplateId($this->getEmailTemplate());
        $email->setParams(
            [
                "ID_USER" => $object->getUser()->getId(),
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
        return EsputnikEmailTemplate::BALANCE_WITHDRAW_FOR_ADMIN;
    }
}