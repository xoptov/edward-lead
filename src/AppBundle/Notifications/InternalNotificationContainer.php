<?php

namespace AppBundle\Notifications;

use AppBundle\Entity\ClientAccount;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\Lead;
use AppBundle\Entity\Member;
use AppBundle\Entity\Message;
use AppBundle\Entity\Room;
use AppBundle\Entity\Thread;
use AppBundle\Entity\Trade;
use AppBundle\Entity\User;
use AppBundle\Entity\Withdraw;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
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
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * EmailNotificationContainer constructor.
     *
     * @param InternalClient         $client
     * @param UrlGeneratorInterface  $router
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(InternalClient $client, UrlGeneratorInterface $router, EntityManagerInterface $entityManager)
    {
        $this->client = $client;
        $this->router = $router;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Room $object
     *
     */
    public function newRoomCreated(Room $object): void
    {
        $message = "Вы создали комнату #{$object->getId()} - {$object->getName()}";
        $link = $this->router->generate('app_room_view', ['id' => $object->getId()]);
        $html = "<p class='notice__item__txt'>{$message}</p><div class='notice__item__foot fx fx-wrap fx-between'><a href='{$link}' class='btn btn__green notice__item__btn'>Перейти</a></div>";
        $user = $object->getOwner();

        $notification = new Notification($user, $html);

        $this->client->send($notification);
    }

    /**
     * @param Room $object
     *
     */
    public function roomDeactivated(Room $object): void
    {
        $message = "Вы деактивровали комнату #{$object->getId()} - {$object->getName()}";
        $html = "<p class='notice__item__txt'>{$message}</p>";
        $user = $object->getOwner();

        $notification = new Notification($user, $html);

        $this->client->send($notification);
    }

    /**
     * @param Invoice $object
     *
     */
    public function invoiceProcessed(Invoice $object): void
    {
        $message = "Ваш баланс пополнен на сумму {$object->getAmount(100)}";
        $html = "<p class='notice__item__txt'>{$message}</p>";
        $user = $object->getUser();

        $notification = new Notification($user, $html);

        $this->client->send($notification);
    }

    /**
     * @param ClientAccount $object
     *
     */
    public function accountBalanceApproachingZero(ClientAccount $object): void
    {
        $message = "Ваш баланс приближается к нулю. Не забудьте его пополнить.";
        $link = $this->router->generate('app_financial_deposit');
        $html = "<p class='notice__item__txt'>{$message}</p><div class='notice__item__foot fx fx-wrap fx-between'><a href='{$link}' class='btn btn__green notice__item__btn'>Пополнить баланс</a></div>";
        $user = $object->getUser();
        $type = Notification::TYPE_IMPORTANT;

        $notification = new Notification($user, $html, $type);

        $this->client->send($notification);
    }

    /**
     * @param ClientAccount $object
     *
     */
    public function accountBalanceLowerThenMinimal(ClientAccount $object): void
    {
        $message = "Ваш баланс менее 40 рублей. Пополните баланс для дальнейшей работы.";
        $link = $this->router->generate('app_financial_deposit');
        $html = "<p class='notice__item__txt'>{$message}</p><div class='notice__item__foot fx fx-wrap fx-between'><a href='{$link}' class='btn btn__green notice__item__btn'>Пополнить баланс</a></div>";
        $user = $object->getUser();
        $type = Notification::TYPE_IMPORTANT;

        $notification = new Notification($user, $html, $type);

        $this->client->send($notification);
    }

    /**
     * @param Message $object
     *
     */
    public function messageCreated(Message $object): void
    {
        $message = "У вас имеется новое сообщение от службы поддержки";
        $link = $this->router->generate('app_arbitration');
        $html = "<p class='notice__item__txt'>{$message}</p><div class='notice__item__foot fx fx-wrap fx-between'><a href='{$link}' class='btn btn__green notice__item__btn'>Смотреть</a></div>";
        $user = $object->getThread()->getCreatedBy();
        $type = Notification::TYPE_IMPORTANT;

        if(!$user instanceof User){
            return;
        }

        $notification = new Notification($user, $html, $type);

        $this->client->send($notification);
    }

    /**
     * @param Trade $trade
     *
     */
    public function tradeAccepted(Trade $trade): void
    {
        $message = "По лиду {$trade->getLead()->getId()} арбитраж установил статус \"Завершена Успешно\". Подробнее в разделе \"Арбитраж\"";
        $html = "<p class='notice__item__txt'>{$message}</p>";
        $user = $trade->getSeller();

        $notification = new Notification($user, $html);

        $this->client->send($notification);
    }

    /**
     * @param Trade $trade
     *
     */
    public function tradeRejected(Trade $trade): void
    {
        $message = "По лиду {$trade->getLead()->getId()} арбитраж установил статус \"Откланена\". Подробнее в разделе \"Арбитраж\"";
        $html = "<p class='notice__item__txt'>{$message}</p>";
        $user = $trade->getSeller();

        $notification = new Notification($user, $html);

        $this->client->send($notification);
    }


    public function messageAboutLead(Message $object)
    {
        $thread = $object->getThread();
        $user = $thread->getCreatedBy();

        if (
            !$user instanceof User ||
            !$thread instanceof Thread ||
            !$thread->getLead() instanceof Lead ||
            $thread->getTypeAppeal() !== Thread::TYPE_ARBITRATION
        ) {
            return;
        }

        $message = "Ваи поступило сообщение от службы поддержки в арбитраже по лиду {$thread->getLead()->getId()}";
        $link = $this->router->generate('app_arbitration');
        $html = "<p class='notice__item__txt'>{$message}</p><div class='notice__item__foot fx fx-wrap fx-between'><a href='{$link}' class='btn btn__green notice__item__btn'>Смотреть</a></div>";

        $type = Notification::TYPE_IMPORTANT;

        $notification = new Notification($user, $html, $type);

        $this->client->send($notification);
    }

    /**
     * @param Member $object
     *
     */
    public function someOneJoinedToYou(Member $object): void
    {
        $members = $this->entityManager->getRepository(Member::class)->findBy(['room' => $object->getRoom()]);

        /** @var Member $member */
        foreach ($members as $member) {

            if ($member->getId() === $object->getId()) {
                continue;
            }

            $message = "Пользователь {$member->getUser()->getName()} присоеденился к комнате #{$member->getRoom()->getId()} - {$member->getRoom()->getName()} в качестве {$member->getUser()->getAccount()->getType()}";
            $html = "<p class='notice__item__txt'>{$message}</p>";
            $user = $member->getUser();

            $notification = new Notification($user, $html);

            $this->client->send($notification);
        }
    }

    /**
     * @param Member $object
     *
     */
    public function youJoinedToRoom(Member $object): void
    {
        $message = "Вы успешно присоединились к комнате #{$object->getRoom()->getId()} - {$object->getRoom()->getName()}";
        $html = "<p class='notice__item__txt'>{$message}</p>";
        $user = $object->getUser();

        $notification = new Notification($user, $html);

        $this->client->send($notification);
    }

    /**
     * @param Member $object
     *
     */
    public function youRemovedFromRoom(Member $object): void
    {
        $message = "Вы были изключены из комнаты #{$object->getRoom()->getId()} - {$object->getRoom()->getName()}";
        $html = "<p class='notice__item__txt'>{$message}</p>";
        $user = $object->getUser();

        $notification = new Notification($user, $html);

        $this->client->send($notification);
    }

    /**
     * @param Lead $object
     *
     */
    public function leadNewPlaced(Lead $object): void
    {

        if (!$object->getRoom()) {
            return;
        }

        $members = $this->entityManager->getRepository(Member::class)->findBy(['room' => $object->getRoom()]);

        foreach ($members as $member) {

            /** @var Member $member */
            if (!$member->isCompany()) {
                continue;
            }

            $message = "В комнате #{$object->getRoom()->getId()} появился новый лид - #{$object->getId()}";
            $html = "<p class='notice__item__txt'>{$message}</p>";
            $user = $member->getUser();

            $notification = new Notification($user, $html);

            $this->client->send($notification);
        }
    }

    /**
     * @param Withdraw $object
     *
     */
    public function withdrawUser(Withdraw $object): void
    {
        $message = "Запрос на вывод средств отправлен. Ожидайте ответа администрации";
        $html = "<p class='notice__item__txt'>{$message}</p>";
        $user = $object->getUser();

        $notification = new Notification($user, $html);

        $this->client->send($notification);
    }
}