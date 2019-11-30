<?php

namespace AppBundle\Notifications;

use AppBundle\Entity\ClientAccount;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\Lead;
use AppBundle\Entity\Member;
use AppBundle\Entity\Message;
use AppBundle\Entity\Trade;
use AppBundle\Entity\User;
use AppBundle\Entity\Withdraw;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use NotificationBundle\Client\Client;
use NotificationBundle\Constants\EsputnikEmailTemplate;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EmailNotificationContainer
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * EmailNotificationContainer constructor.
     *
     * @param Client                 $client
     * @param UrlGeneratorInterface  $router
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(Client $client, UrlGeneratorInterface $router, EntityManagerInterface $entityManager)
    {
        $this->client = $client;
        $this->router = $router;
        $this->entityManager = $entityManager;
    }

    /**
     * @param ClientAccount $object
     *
     * @throws Exception
     */
    public function accountBalanceApproachingZero(ClientAccount $object): void
    {
        $this->client->send([
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
        $this->client->send([
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
        $this->client->send([
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
        $this->client->send([
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
     * @param Member $object
     *
     * @throws Exception
     */
    public function noVisitTooLong(Member $object): void
    {
        $this->client->send([
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
        $users = $this->getNotificationOperators();

        /** @var User $user */
        foreach ($users as $user) {

            $this->client->send([
                "to_email" => $user->getEmail(),
                "template_id" => EsputnikEmailTemplate::NEW_SUPPORT_CONTACT,
                "params" => [
                    "ID_SUPPORT" => $object->getThread()->getId(),
                    "ID_USER" => $object->getSender()->getId(),
                ],
            ]);

        }

    }

    /**
     * @param Message $object
     *
     * @throws Exception
     */
    public function messageSupportReply(Message $object): void
    {
        $this->client->send([
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
        $this->client->send([
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
        $users = $this->getNotificationOperators();

        /** @var User $user */
        foreach ($users as $user) {

            $this->client->send([
                "to_email" => $user->getEmail(),
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

    }

    /**
     * @param User $object
     *
     * @throws Exception
     */
    public function userApiTokenChanged(User $object): void
    {
        $this->client->send([
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
        $this->client->send([
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

        $this->client->send([
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
        $this->client->send([
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
        $users = $this->getNotificationOperators();

        /** @var User $user */
        foreach ($users as $user) {

            $this->client->send([
                "to_email" => $user->getEmail(),
                "template_id" => EsputnikEmailTemplate::BALANCE_WITHDRAW_FOR_ADMIN,
                "params" => [
                    "ID_USER" => $object->getUser()->getId(),
                    "BALANCE_LOGOUT" => $object->getAmount(),
                    "ID_BALANCE_LOGOUT" => $object->getId(),
                ],
            ]);
        }

    }

    /**
     * @param Withdraw $object
     *
     * @throws Exception
     */
    public function withdrawRejected(Withdraw $object): void
    {
        $this->client->send([
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
        $this->client->send([
            "to_email" => $object->getUser()->getEmail(),
            "template_id" => EsputnikEmailTemplate::BALANCE_WITHDRAW_FOR_USER,
            "params" => [
                "BALANCE_LOGOUT" => $object->getAmount(),
                "ID_BALANCE_LOGOUT" => $object->getId(),
            ],
        ]);
    }

    /**
     * @return array
     */
    private function getNotificationOperators()
    {
        /** @var UserRepository $repository */
        $repository = $this->entityManager->getRepository(User::class);

        return $repository->getByRole(User::ROLE_NOTIFICATION_OPERATOR);

    }
}