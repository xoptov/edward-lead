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
use NotificationBundle\Client\Client;
use NotificationBundle\Constants\EsputnikEmailTemplate;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EmailNotificationContainer
{
    /**
     * @var Client
     */
    private $emailClient;

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
     * @param Client                $emailClient
     * @param UrlGeneratorInterface $router
     * @param string                $adminEmail
     */
    public function __construct(Client $emailClient, UrlGeneratorInterface $router, string $adminEmail)
    {
        $this->emailClient = $emailClient;
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
        $this->emailClient->send([
            "to_email" => $object->getUser()->getEmail(),
            "template_id" => EsputnikEmailTemplate::BALANCE_LOW,
            "params" => [
                "NAME" => $object->getUser()->getName(),
                "BALANCE" => $object->getBalance()
            ],
        ]);
    }

    /**
     * @param ClientAccount $object
     *
     * @throws Exception
     */
    public function accountBalanceLowerThenMinimal(ClientAccount $object): void
    {
        $this->emailClient->send([
            "to_email" => $object->getUser()->getEmail(),
            "template_id" => EsputnikEmailTemplate::BALANCE_LOWER_THEN_MINIMAL,
            "params" => [
                "NAME" => $object->getUser()->getName(),
            ],
        ]);
    }

    /**
     * @param Invoice $object
     *
     * @throws Exception
     */
    public function invoiceProcessed(Invoice $object): void
    {
        $this->emailClient->send([
            "to_email" => $object->getUser()->getEmail(),
            "template_id" => EsputnikEmailTemplate::BALANCE_CHARGED,
            "params" => [
                "NAME" => $object->getUser()->getName(),
                "SUMM_BALANCE" => $object->getAmount(),
                "TYPE_BALANCE" => $object->getDescription(),
            ],
        ]);
    }

    /**
     * @param Lead $object
     *
     * @throws Exception
     */
    public function leadNewPlaced(Lead $object): void
    {
        $this->emailClient->send([
            "to_email" => $object->getUser()->getEmail(),
            "template_id" => EsputnikEmailTemplate::NEW_LEAD_IN_ROOM,
            "params" => [
                "ID_ROOM" => $object->getRoom()->getId(),
                "NAME_ROOM" => $object->getRoom()->getName(),
                "URL_ROOM" => $this->router->generate('app_room_view', ['id' => $object->getRoom()->getId()]),
                "ID_LEAD" => $object->getId()
            ],
        ]);
    }

    /**
     * @param Lead $object
     *
     * @throws Exception
     */
    public function noVisitTooLong(Lead $object): void
    {
        $this->emailClient->send([
            "to_email" => $object->getUser()->getEmail(),
            "template_id" => EsputnikEmailTemplate::NO_VISITING_FOR_TOO_LONG,
            "params" => [],
        ]);
    }

    /**
     * @param Message $object
     *
     * @throws Exception
     */
    public function messageCreated(Message $object): void
    {
        $this->emailClient->send([
            "to_email" => $this->adminEmail,
            "template_id" => EsputnikEmailTemplate::NEW_SUPPORT_CONTACT,
            "params" => [
                "ID_SUPPORT" => $object->getThread()->getId(),
                "ID_USER" => $object->getSender()->getId(),
            ],
        ]);
    }

    /**
     * @param Message $object
     *
     * @throws Exception
     */
    public function messageSupportReply(Message $object): void
    {
        $this->emailClient->send([
            "to_email" => $object->getSender()->getEmail(),
            "template_id" => EsputnikEmailTemplate::SUPPORT_RESPONSE,
            "params" => [
                "ID_SUPPORT" => $object->getThread()->getId(),
                "TEXT_SUPPORT" => $object->getBody(),
            ],
        ]);
    }

    /**
     * @param User $object
     *
     * @throws Exception
     */
    public function newUserRegistered(User $object): void
    {
        $this->emailClient->send([
            "to_email" => $object->getEmail(),
            "template_id" => EsputnikEmailTemplate::REGISTRATION,
            "params" => ["name" => $object->getName()],
        ]);
    }

    /**
     * @param Trade $object
     *
     * @throws Exception
     */
    public function tradeProceeding(Trade $object): void
    {
        $this->emailClient->send([
            "to_email" => $this->adminEmail,
            "template_id" => EsputnikEmailTemplate::NEW_ARBITRAJ,
            "params" => [
                "ID_ARBITRATION" => $object->getId(),
                "ID_LEAD" => $object->getLead()->getId(),
                "ID_WEBMASTER" => $object->getSellerId(),
                "ID_COMPANY" => $object->getBuyerId(),
                "SUMM_DEAL" => $object->getAmount(),
                "ID_ROOM" => $object->getLead()->getRoom()->getId(),
            ],
        ]);
    }

    /**
     * @param User $object
     *
     * @throws Exception
     */
    public function userApiTokenChanged(User $object): void
    {
        $this->emailClient->send([
            "to_email" => $object->getEmail(),
            "template_id" => EsputnikEmailTemplate::API_KEY_CHANGE,
            "params" => ["KEYAPI" => $object->getToken()],
        ]);
    }

    /**
     * @param User $object
     *
     * @throws Exception
     */
    public function userPasswordChanged(User $object): void
    {
        $this->emailClient->send([
            "to_email" => $object->getEmail(),
            "template_id" => EsputnikEmailTemplate::PASSWORD_CHANGE_SUCCESS,
            "params" => [],
        ]);
    }

    /**
     * @param User $object
     *
     * @throws Exception
     */
    public function userResetTokenUpdated(User $object): void
    {
        $url = $this->router->generate('app_password_reset_confirm', ['token' => $object->getResetToken()]);

        $this->emailClient->send([
            "to_email" => $object->getEmail(),
            "template_id" => EsputnikEmailTemplate::PASSWORD_CHANGE_REQUEST,
            "params" => ["URL_PASSWORD" => $url],
        ]);
    }

    /**
     * @param Withdraw $object
     *
     * @throws Exception
     */
    public function withdrawAccepted(Withdraw $object): void
    {
        $this->emailClient->send([
            "to_email" => $object->getUser()->getEmail(),
            "template_id" => EsputnikEmailTemplate::BALANCE_WITHDRAW_SUCCESS,
            "params" => [
                "BALANCE_LOGOUT" => $object->getAmount(),
                "ID_BALANCE_LOGOUT" => $object->getId(),
                "TYPE_BALANCE_LOGOUT" => $object->getDescription(),
            ],
        ]);
    }

    /**
     * @param Withdraw $object
     *
     * @throws Exception
     */
    public function withdrawAdmin(Withdraw $object): void
    {
        $this->emailClient->send([
            "to_email" => $this->adminEmail,
            "template_id" => EsputnikEmailTemplate::BALANCE_WITHDRAW_FOR_ADMIN,
            "params" => [
                "ID_USER" => $object->getUser()->getId(),
                "BALANCE_LOGOUT" => $object->getAmount(),
                "ID_BALANCE_LOGOUT" => $object->getId(),
            ],
        ]);
    }

    /**
     * @param Withdraw $object
     *
     * @throws Exception
     */
    public function withdrawRejected(Withdraw $object): void
    {
        $this->emailClient->send([
            "to_email" => $object->getUser()->getEmail(),
            "template_id" => EsputnikEmailTemplate::BALANCE_WITHDRAW_FAIL,
            "params" => [
                "BALANCE_LOGOUT" => $object->getAmount(),
                "ID_BALANCE_LOGOUT" => $object->getId(),
            ],
        ]);
    }

    /**
     * @param Withdraw $object
     *
     * @throws Exception
     */
    public function withdrawUser(Withdraw $object): void
    {
        $this->emailClient->send([
            "to_email" => $object->getUser()->getEmail(),
            "template_id" => EsputnikEmailTemplate::BALANCE_WITHDRAW_FOR_USER,
            "params" => [
                "BALANCE_LOGOUT" => $object->getAmount(),
                "ID_BALANCE_LOGOUT" => $object->getId(),
            ],
        ]);
    }
}