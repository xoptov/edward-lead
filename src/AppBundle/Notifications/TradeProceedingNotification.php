<?php

namespace AppBundle\Notifications;

use AppBundle\Entity\Trade;
use Exception;
use NotificationBundle\ChannelModels\Email;
use NotificationBundle\Channels\EmailChannel;
use NotificationBundle\Constants\EsputnikEmailTemplate;

class TradeProceedingNotification implements Notification
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
     * @param Trade $object
     *
     * @throws Exception
     */
    public function send(Trade $object): void
    {
        $this->sendEmail($object);
    }

    /**
     * @param Trade $object
     *
     * @throws Exception
     */
    private function sendEmail(Trade $object): void
    {
        $email = new Email;

        $email->setToEmail($this->adminEmail);
        $email->setTemplateId($this->getEmailTemplate());
        $email->setParams(
            [
                "ID_ARBITRATION" => $object->getId(),
                "ID_LEAD" => $object->getLead()->getId(),
                "ID_WEBMASTER" => $object->getSellerId(),
                "ID_COMPANY" => $object->getBuyerId(),
                "SUMM_DEAL" => $object->getAmount(),
                "ID_ROOM" => $object->getLead()->getRoom()->getId(),
            ]
        );

        $this->emailChannel->send($email);
    }

    /**
     * @return string
     */
    private function getEmailTemplate(): string
    {
        return EsputnikEmailTemplate::NEW_ARBITRAJ;
    }
}