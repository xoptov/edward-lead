<?php

namespace AppBundle\Notifications;

use AppBundle\Entity\User;
use Exception;
use NotificationBundle\ChannelModels\Email;
use NotificationBundle\Channels\EmailChannel;
use NotificationBundle\Constants\EsputnikEmailTemplate;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserResetTokenUpdatedNotification implements Notification
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
     * OnUserRegisterNotification constructor.
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

        $url = $this->router->generate('app_password_reset_confirm', ['token' => $object->getResetToken()]);
        $email = new Email;

        $email->setToEmail($object->getEmail());
        $email->setTemplateId($this->getEmailTemplate());
        $email->setParams(
            [
                "URL_PASSWORD" => $url,
            ]
        );

        $this->emailChannel->send($email);
    }

    /**
     * @return string
     */
    private function getEmailTemplate(): string
    {
        return EsputnikEmailTemplate::PASSWORD_CHANGE_REQUEST;
    }
}