<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Thread;
use AppBundle\Entity\User;
use AppBundle\Event\LeadEvent;
use Doctrine\ORM\EntityManagerInterface;
use FOS\MessageBundle\MessageBuilder\NewThreadMessageBuilder;
use FOS\MessageBundle\Sender\SenderInterface;
use FOS\MessageBundle\Composer\ComposerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LeadSubscriber implements EventSubscriberInterface
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
            LeadEvent::NO_TARGET => 'handleNoTarget'
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
     * @param LeadEvent $event
     */
    public function handleNoTarget(LeadEvent $event)
    {
        $lead = $event->getLead();

        if (!$lead->hasTrade()) {
            return;
        }

        $buyer = $lead->getBuyer();

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
            ->setBody(sprintf('Лид №%d не целевой, прошу Вас помочь с решением вопроса.', $lead->getId()));

        foreach ($admins as $admin) {
            $threadBuilder->addRecipient($admin);
        }

        $message = $threadBuilder->getMessage();

        /** @var Thread $thread */
        $thread = $message->getThread();
        $thread
            ->setLead($lead)
            ->setStatus(Thread::STATUS_NEW)
            ->setTypeAppeal(Thread::TYPE_ARBITRATION);

        $this->fosSender->send($message);
    }
}