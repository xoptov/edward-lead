<?php

namespace AppBundle\Controller\Admin;


use Sonata\AdminBundle\Controller\CRUDController;

class ThreadController extends CRUDController
{
    /**
     * Создание тестовой ветки сообщений
     *
     * ToDo удалить позднее
     */
    public function createAction()
    {
        $sender = $this->getUser();

        $threadBuilder = $this->get('fos_message.composer')->newThread();

        $threadBuilder
            //->addRecipient($recipient) // Retrieved from your backend, your user manager or ...
            ->setSender($sender)
            ->setSubject('Stof commented on your pull request #456789')
            ->setBody('You have a typo, : mondo instead of mongo. Also for coding standards ...');

        $sender = $this->get('fos_message.sender');
        $sender->send($threadBuilder->getMessage());
    }
}