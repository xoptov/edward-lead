<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Thread;
use AppBundle\Entity\User;
use Sonata\AdminBundle\Controller\CRUDController;

class ThreadController extends CRUDController
{
    /**
     * Создание тестовой ветки сообщений
     * Этот бред удалю когда закончу задачи!
     *
     * ToDo удалить позднее
     */
    public function createAction()
    {
        $sender = $this->getUser();

        $threadBuilder = $this->get('fos_message.composer')->newThread();

        $threadBuilder
            ->addRecipient($this->getDoctrine()->getRepository(User::class)->find(2)) // Retrieved from your backend, your user manager or ...
            ->setSender($sender)
            ->setSubject('Stof commented on your pull request #456789')
            ->setBody('You have a typo, : mondo instead of mongo. Also for coding standards ...');

        $message = $threadBuilder->getMessage();

        /** @var Thread $thread */
        $thread = $message->getThread();
        $thread->setStatus(Thread::STATUS_NEW);
        $thread->setTypeAppeal(Thread::TYPE_ARBITRATION);

        $sender = $this->get('fos_message.sender');
        $sender->send($message);
    }
}