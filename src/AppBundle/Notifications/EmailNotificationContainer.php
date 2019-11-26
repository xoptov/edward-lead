<?php

namespace AppBundle\Notifications;

use AppBundle\Entity\ClientAccount;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\Lead;
use AppBundle\Entity\Message;
use AppBundle\Entity\Trade;
use AppBundle\Entity\User;
use AppBundle\Entity\Withdraw;
use Exception;
use NotificationBundle\ChannelModels\Email;
use NotificationBundle\Channels\EmailChannel;
use NotificationBundle\Constants\EsputnikEmailTemplate;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EmailNotificationContainer
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
     * @var string
     */
    private $adminEmail;

    /**
     * EmailNotificationContainer constructor.
     *
     * @param EmailChannel          $emailChannel
     * @param UrlGeneratorInterface $router
     * @param string                $adminEmail
     */
    public function __construct(EmailChannel $emailChannel, UrlGeneratorInterface $router, string $adminEmail)
    {
        $this->emailChannel = $emailChannel;
        $this->router = $router;
        $this->adminEmail = $adminEmail;
    }

    /**
     * @param ClientAccount $object
     *
     * @throws Exception
     */
    public function accountBalanceApproachingZero(ClientAccount $object): void
    {
        $email = new Email;

        $email->setToEmail($object->getUser()->getEmail());
        $email->setTemplateId(EsputnikEmailTemplate::BALANCE_LOW);
        $email->setParams(
            [
                "NAME" => $object->getUser()->getName(),
                "BALANCE" => $object->getBalance()
            ]
        );

        $this->emailChannel->send($email);
    }

    /**
     * @param ClientAccount $object
     *
     * @throws Exception
     */
    public function accountBalanceLowerThenMinimal(ClientAccount $object): void
    {
        $email = new Email;

        $email->setToEmail($object->getUser()->getEmail());
        $email->setTemplateId(EsputnikEmailTemplate::BALANCE_LOWER_THEN_MINIMAL);
        $email->setParams(
            [
                "NAME" => $object->getUser()->getName(),
            ]
        );

        $this->emailChannel->send($email);
    }

    /**
     * @param Invoice $object
     *
     * @throws Exception
     */
    public function invoiceProcessed(Invoice $object): void
    {
        $email = new Email;

        $email->setToEmail($object->getUser()->getEmail());
        $email->setTemplateId(EsputnikEmailTemplate::BALANCE_CHARGED);
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
     * @param Lead $object
     *
     * @throws Exception
     */
    public function leadNewPlaced(Lead $object): void
    {
        $email = new Email;

        $email->setToEmail($object->getUser()->getEmail());
        $email->setTemplateId(EsputnikEmailTemplate::NEW_LEAD_IN_ROOM);
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
     * @param Lead $object
     *
     * @throws Exception
     */
    public function noVisitTooLong(Lead $object): void
    {
        $email = new Email;

        $email->setToEmail($object->getUser()->getEmail());
        $email->setTemplateId(EsputnikEmailTemplate::NO_VISITING_FOR_TOO_LONG);

        $this->emailChannel->send($email);
    }

    /**
     * @param Message $object
     *
     * @throws Exception
     */
    public function messageCreated(Message $object): void
    {
        $email = new Email;

        $email->setToEmail($this->adminEmail);
        $email->setTemplateId(EsputnikEmailTemplate::NEW_SUPPORT_CONTACT);
        $email->setParams(
            [
                "ID_SUPPORT" => $object->getThread()->getId(),
                "ID_USER" => $object->getSender()->getId(),
            ]
        );

        $this->emailChannel->send($email);
    }

    /**
     * @param Message $object
     *
     * @throws Exception
     */
    public function messageSupportReply(Message $object): void
    {
        $email = new Email;

        $email->setToEmail($object->getSender()->getEmail());
        $email->setTemplateId(EsputnikEmailTemplate::SUPPORT_RESPONSE);
        $email->setParams(
            [
                "ID_SUPPORT" => $object->getThread()->getId(),
                "TEXT_SUPPORT" => $object->getBody(),
            ]
        );

        $this->emailChannel->send($email);
    }

    /**
     * @param User $object
     *
     * @throws Exception
     */
    public function newUserRegistered(User $object): void
    {
        $email = new Email;

        $email->setToEmail($object->getEmail());
        $email->setTemplateId(EsputnikEmailTemplate::REGISTRATION);
        $email->setParams(["name" => $object->getName()]);

        $this->emailChannel->send($email);
    }

    /**
     * @param Trade $object
     *
     * @throws Exception
     */
    public function tradeProceeding(Trade $object): void
    {
        $email = new Email;

        $email->setToEmail($this->adminEmail);
        $email->setTemplateId(EsputnikEmailTemplate::NEW_ARBITRAJ);
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
     * @param User $object
     *
     * @throws Exception
     */
    public function userApiTokenChanged(User $object): void
    {
        $email = new Email;

        $email->setToEmail($object->getEmail());
        $email->setTemplateId(EsputnikEmailTemplate::API_KEY_CHANGE);
        $email->setParams(["KEYAPI" => $object->getToken()]);

        $this->emailChannel->send($email);
    }

    /**
     * @param User $object
     *
     * @throws Exception
     */
    public function userPasswordChanged(User $object): void
    {
        $email = new Email;

        $email->setToEmail($object->getEmail());
        $email->setTemplateId(EsputnikEmailTemplate::PASSWORD_CHANGE_SUCCESS);

        $this->emailChannel->send($email);
    }

    /**
     * @param User $object
     *
     * @throws Exception
     */
    public function userResetTokenUpdated(User $object): void
    {
        $url = $this->router->generate('app_password_reset_confirm', ['token' => $object->getResetToken()]);
        $email = new Email;

        $email->setToEmail($object->getEmail());
        $email->setTemplateId(EsputnikEmailTemplate::PASSWORD_CHANGE_REQUEST);
        $email->setParams(["URL_PASSWORD" => $url]);

        $this->emailChannel->send($email);
    }

    /**
     * @param Withdraw $object
     *
     * @throws Exception
     */
    public function withdrawAccepted(Withdraw $object): void
    {
        $email = new Email;

        $email->setToEmail($object->getUser()->getEmail());
        $email->setTemplateId(EsputnikEmailTemplate::BALANCE_WITHDRAW_SUCCESS);
        $email->setParams(
            [
                "BALANCE_LOGOUT" => $object->getAmount(),
                "ID_BALANCE_LOGOUT" => $object->getId(),
                "TYPE_BALANCE_LOGOUT" => $object->getDescription(),
            ]
        );

        $this->emailChannel->send($email);
    }

    /**
     * @param Withdraw $object
     *
     * @throws Exception
     */
    public function withdrawAdmin(Withdraw $object): void
    {
        $email = new Email;

        $email->setToEmail($this->adminEmail);
        $email->setTemplateId(EsputnikEmailTemplate::BALANCE_WITHDRAW_FOR_ADMIN);
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
     * @param Withdraw $object
     *
     * @throws Exception
     */
    public function withdrawRejected(Withdraw $object): void
    {
        $email = new Email;

        $email->setToEmail($object->getUser()->getEmail());
        $email->setTemplateId(EsputnikEmailTemplate::BALANCE_WITHDRAW_FAIL);
        $email->setParams(
            [
                "BALANCE_LOGOUT" => $object->getAmount(),
                "ID_BALANCE_LOGOUT" => $object->getId(),
            ]
        );

        $this->emailChannel->send($email);
    }

    /**
     * @param Withdraw $object
     *
     * @throws Exception
     */
    public function withdrawUser(Withdraw $object): void
    {
        $email = new Email;

        $email->setToEmail($object->getUser()->getEmail());
        $email->setTemplateId(EsputnikEmailTemplate::BALANCE_WITHDRAW_FOR_USER);
        $email->setParams(
            [
                "BALANCE_LOGOUT" => $object->getAmount(),
                "ID_BALANCE_LOGOUT" => $object->getId(),
            ]
        );

        $this->emailChannel->send($email);
    }
}