<?php

namespace AppBundle\Notifications;

use AppBundle\Entity\Lead;
use Exception;
use NotificationBundle\ChannelModels\Email;
use NotificationBundle\Channels\EmailChannel;
use NotificationBundle\Constants\EsputnikEmailTemplate;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LeadNewPlacedNotification implements Notification
{
    /**
     * @var EmailChannel
     */
    private $emailChannel;
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * RoomCreatedNotification constructor.
     *
     * @param EmailChannel          $emailChannel
     * @param UrlGeneratorInterface $router
     */
    public function __construct(EmailChannel $emailChannel, UrlGeneratorInterface $router)
    {
        $this->emailChannel = $emailChannel;
        $this->router = $router;
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
        $email->setParams(
            [
                "ID_ROOM" => $object->getRoom()->getId(),
                "NAME_ROOM" => $object->getRoom()->getName(),
                "URL_ROOM" => $this->router->generate('app_room_view', ['id' => $object->getRoom()->getId()]),
                "ID_LEAD" => $object->getId()
            ]
        );

        $this->emailChannel->send($email);
    }

    /**
     * @return string
     */
    private function getEmailTemplate(): string
    {
        return EsputnikEmailTemplate::NEW_LEAD_IN_ROOM;
    }
}