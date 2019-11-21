<?php

namespace NotificationBundle\Clients;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use NotificationBundle\Clients\Interfaces\InternalClientInterface;
use NotificationBundle\Entity\Notification;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class InternalClient extends BaseClient implements InternalClientInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * InternalChannel constructor.
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct($validator);
    }

    public function sendToDb(Notification $model): void
    {

        $this->validate($model);

        $this->entityManager->persist($model);
        $this->entityManager->flush();

    }

}