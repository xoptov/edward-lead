<?php

namespace AppBundle\Notifications;

use AppBundle\Entity\ClientAccount;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\Lead;
use AppBundle\Entity\Member;
use AppBundle\Entity\Message;
use AppBundle\Entity\Room;
use AppBundle\Entity\Trade;
use AppBundle\Entity\Withdraw;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use NotificationBundle\Client\Client;
use NotificationBundle\Client\InternalClient;
use NotificationBundle\Entity\Notification;
use NotificationBundle\Exception\ValidationNotificationClientException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class InternalNotificationContainer
{
    /**
     * @var InternalClient
     */
    private $client;
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * EmailNotificationContainer constructor.
     *
     * @param InternalClient        $client
     * @param UrlGeneratorInterface $router
     */
    public function __construct(InternalClient $client, UrlGeneratorInterface $router)
    {
        $this->client = $client;
        $this->router = $router;
    }

    /**
     * @param Room $object
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ValidationNotificationClientException
     */
    public function newRoomCreated(Room $object): void
    {
        $message = "Вы создали комнату #{$object->getId()} - {$object->getName()}";
        $link = $this->router->generate('app_room_view', ['id' => $object->getRoom()->getId()]);
        $html = "<div class='notification'><p>{$message}</p><a class='notification__button' href='{$link}'>Перейти</a></div>";
        $user = $object->getOwner();

        $notification = new Notification($user, $html);

        $this->client->send($notification);

    }

    /**
     * @param Room $object
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ValidationNotificationClientException
     */
    public function roomDeactivated(Room $object): void
    {
        $message = "Вы деактивровали комнату #{$object->getId()} - {$object->getName()}";
        $html = "<div class='notification'><p>{$message}</p></div>";
        $user = $object->getOwner();

        $notification = new Notification($user, $html);

        $this->client->send($notification);
    }

    /**
     * @param Invoice $object
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ValidationNotificationClientException
     */
    public function invoiceProcessed(Invoice $object): void
    {
        $message = "Ваш баланс пополнен на сумму {$object->getAmount()}";
        $html = "<div class='notification'><p>{$message}</p></div>";
        $user = $object->getUser();

        $notification = new Notification($user, $html);

        $this->client->send($notification);
    }

    /**
     * @param ClientAccount $object
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ValidationNotificationClientException
     */
    public function accountBalanceApproachingZero(ClientAccount $object): void
    {
        $message = "Ваш баланс приближается к нулю. Не забудьте его пополнить.";
        $link = null; // TODO get link
        $html = "<div class='notification'><p>{$message}</p><a class='notification__button' href='{$link}'>Пополнить баланс</a></div>";
        $user = $object->getUser();
        $type = Notification::TYPE_IMPORTANT;

        $notification = new Notification($user, $html, $type);

        $this->client->send($notification);
    }

    /**
     * @param ClientAccount $object
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ValidationNotificationClientException
     */
    public function accountBalanceLowerThenMinimal(ClientAccount $object): void
    {
        $message = "Ваш баланс менее 40 рублей. Пополните баланс для дальнейшей работы.";
        $link = null; // TODO get link
        $html = "<div class='notification'><p>{$message}</p><a class='notification__button' href='{$link}'>Пополнить баланс</a></div>";
        $user = $object->getUser();
        $type = Notification::TYPE_IMPORTANT;

        $notification = new Notification($user, $html, $type);

        $this->client->send($notification);
    }

    /**
     * @param Message $object
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ValidationNotificationClientException
     */
    public function messageCreated(Message $object): void
    {
        $message = "У вас имеется новое сообщение от службы поддержки";
        $link = null; // TODO get link
        $html = "<div class='notification'><p>{$message}</p><a class='notification__button' href='{$link}'>Смотреть</a></div>";
        $user = $object->getThread()->getCreatedBy();
        $type = Notification::TYPE_IMPORTANT;

        $notification = new Notification($user, $html, $type);

        $this->client->send($notification);
    }

    /**
     * @param Trade $trade
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ValidationNotificationClientException
     */
    public function tradeAccepted(Trade $trade): void
    {
        // TODO status translate
        $message = "По лиду {$trade->getLead()->getId()} арбитраж установил статус {$trade->getStatus()}. Подробнее в разделе \"арбитраж\"";
        $html = "<div class='notification'><p>{$message}</p></div>";
        $user = $trade->getSeller()->getId();

        $notification = new Notification($user, $html);

        $this->client->send($notification);
    }

    /**
     * @param Trade $trade
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ValidationNotificationClientException
     */
    public function tradeRejected(Trade $trade): void
    {
        $message = "Ваи поступило сообщение от службы поддержки в арбитраже по лиду {$trade->getLead()->getId()}";
        $link = null; // TODO get link
        $html = "<div class='notification'><p>{$message}</p><a class='notification__button' href='{$link}'>Смотреть</a></div>";
        $user = $trade->getSeller()->getId();
        $type = Notification::TYPE_IMPORTANT;

        $notification = new Notification($user, $html, $type);

        $this->client->send($notification);
    }

    /**
     * @param Member $trade
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ValidationNotificationClientException
     */
    public function someOneJoinedToYou(Member $trade): void
    {
        $message = "";
        $html = "<div class='notification'><p>{$message}</p></div>";
        $user = "";

        $notification = new Notification($user, $html);

        $this->client->send($notification);
    }

    /**
     * @param Member $trade
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ValidationNotificationClientException
     */
    public function youJoinedToRoom(Member $trade): void
    {
        $message = "";
        $html = "<div class='notification'><p>{$message}</p></div>";
        $user = "";

        $notification = new Notification($user, $html);

        $this->client->send($notification);
    }

    /**
     * @param Member $trade
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ValidationNotificationClientException
     */
    public function youRemovedFromRoom(Member $trade): void
    {
        $message = "";
        $html = "<div class='notification'><p>{$message}</p></div>";
        $user = "";

        $notification = new Notification($user, $html);

        $this->client->send($notification);
    }

    /**
     * @param Lead $object
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ValidationNotificationClientException
     */
    public function leadNewPlaced(Lead $object): void
    {
        $message = "";
        $html = "<div class='notification'><p>{$message}</p></div>";
        $user = "";

        $notification = new Notification($user, $html);

        $this->client->send($notification);
    }

    /**
     * @param Withdraw $object
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ValidationNotificationClientException
     */
    public function withdrawUser(Withdraw $object): void
    {
        $message = "";
        $html = "<div class='notification'><p>{$message}</p></div>";
        $user = "";

        $notification = new Notification($user, $html);

        $this->client->send($notification);
    }
}