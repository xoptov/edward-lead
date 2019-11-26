<?php

namespace AppBundle\Notifications;

use AppBundle\Entity\Invoice;
use Exception;
use NotificationBundle\ChannelModels\Email;
use NotificationBundle\Channels\EmailChannel;
use NotificationBundle\Constants\EsputnikEmailTemplate;

class InvoiceProcessedNotification implements Notification
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
     * @param Invoice $object
     *
     * @throws Exception
     */
    public function send(Invoice $object): void
    {
        $this->sendEmail($object);
    }

    /**
     * @param Invoice $object
     *
     * @throws Exception
     */
    private function sendEmail(Invoice $object): void
    {
        $email = new Email;

        $email->setToEmail($object->getUser()->getEmail());
        $email->setTemplateId($this->getEmailTemplate());
        $email->setParams(
            [
                "NAME" => $object->getUser()->getName(),
                "SUMM_BALANCE" => $object->getAmount(),
                "TYPE_BALANCE" => $object->getDescription(),
            ]
        );

        $this->emailChannel->send($email);
    }

    /**
     * @return string
     */
    private function getEmailTemplate(): string
    {
        return EsputnikEmailTemplate::BALANCE_CHARGED;
    }
}