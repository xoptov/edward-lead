<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\User;
use AppBundle\Entity\Thread;
use AppBundle\Event\TradeEvent;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\MessageBundle\Sender\SenderInterface;
use FOS\MessageBundle\Composer\ComposerInterface;
use FOS\MessageBundle\MessageBuilder\NewThreadMessageBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TradeSubscriber implements EventSubscriberInterface
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
     * @param EntityManagerInterface $entityManager
     * @param ComposerInterface      $fosComposer
     * @param SenderInterface        $fosSender
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ComposerInterface $fosComposer,
        SenderInterface $fosSender
    ) {
        $this->entityManager = $entityManager;
        $this->fosComposer = $fosComposer;
        $this->fosSender = $fosSender;
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

        /** @var UserRepository */
        $userRepository = $this->entityManager->getRepository(User::class);
        
        $admins = $userRepository
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
        //todo: тут необходимо добавить создание нотификации для продавца.
    }

    /**
     * @param TradeEvent $event
     */
    public function handleReject(TradeEvent $event): void
    {
        //todo: тут необходимо добавить создание нотификации для продавца.
    }
}