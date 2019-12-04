<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Thread;
use AppBundle\Entity\User;
use AppBundle\Event\TradeEvent;
use AppBundle\Notifications\EmailNotificationContainer;
use AppBundle\Notifications\InternalNotificationContainer;
use AppBundle\Notifications\SmsNotificationContainer;
use Doctrine\ORM\EntityManagerInterface;
use FOS\MessageBundle\Composer\ComposerInterface;
use FOS\MessageBundle\MessageBuilder\NewThreadMessageBuilder;
use FOS\MessageBundle\Sender\SenderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TradeSubscriber extends BaseEventListener implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ComposerInterface
     */
    private $fosComposer;

    /**
     * @var SenderInterface
     */
    private $fosSender;

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            TradeEvent::PROCEEDING => 'handleProceeding',
            TradeEvent::ACCEPTED   => 'handleAccept',
            TradeEvent::REJECTED   => 'handleReject'
        ];
    }

    /**
     * TradeSubscriber constructor.
     *
     * @param EntityManagerInterface        $entityManager
     * @param ComposerInterface             $fosComposer
     * @param SenderInterface               $fosSender
     * @param EmailNotificationContainer    $emailNotificationContainer
     * @param SmsNotificationContainer      $smsNotificationContainer
     * @param InternalNotificationContainer $internalNotificationContainer
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ComposerInterface $fosComposer,
        SenderInterface $fosSender,
        EmailNotificationContainer $emailNotificationContainer,
        SmsNotificationContainer $smsNotificationContainer,
        InternalNotificationContainer $internalNotificationContainer
    )
    {
        $this->entityManager = $entityManager;
        $this->fosComposer = $fosComposer;
        $this->fosSender = $fosSender;
        parent::__construct($emailNotificationContainer, $smsNotificationContainer, $internalNotificationContainer);
    }

    /**
     * @param TradeEvent $event
     */
    public function handleProceeding(TradeEvent $event): void
    {
        $trade = $event->getTrade();
        $buyer = $trade->getBuyer();

        if (!$buyer) {
            return;
        }

        $admins = $this->entityManager->getRepository(User::class)
            ->getAdmins();

        if (empty($admins)) {
            return;
        }

        /** @var NewThreadMessageBuilder $threadBuilder */
        $threadBuilder = $this->fosComposer->newThread();

        $threadBuilder
            ->setSubject('Не целевой лид')
            ->setSender($buyer)
            ->setBody(sprintf('Лид №%d не целевой, прошу Вас помочь с решением вопроса.', $trade->getLead()->getId()));

        foreach ($admins as $admin) {
            $threadBuilder->addRecipient($admin);
        }

        $message = $threadBuilder->getMessage();

        /** @var Thread $thread */
        $thread = $message->getThread();
        $thread
            ->setLead($trade->getLead())
            ->setStatus(Thread::STATUS_NEW)
            ->setTypeAppeal(Thread::TYPE_ARBITRATION);

        $this->fosSender->send($message);

        //todo: тут необходимо добавить создания нотификации для продавца.
    }

    /**
     * @param TradeEvent $event
     */
    public function handleAccept(TradeEvent $event): void
    {
        $this->internalNotificationContainer->tradeAccepted($event->getTrade());
        //todo: тут необходимо добавить создание нотификации для продавца.
    }

    /**
     * @param TradeEvent $event
     */
    public function handleReject(TradeEvent $event): void
    {
        //todo: тут необходимо добавить создание нотификации для продавца.
        $this->internalNotificationContainer->tradeRejected($event->getTrade());
    }
}