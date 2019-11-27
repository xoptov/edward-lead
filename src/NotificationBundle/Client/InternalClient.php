<?php

namespace NotificationBundle\Client;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use NotificationBundle\Client\Interfaces\InternalClientInterface;
use NotificationBundle\Entity\Notification;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class InternalClient implements InternalClientInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * InternalClient constructor.
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct($validator);
    }

    public function send(Notification $model): void
    {
        $this->validate($model);

        $this->entityManager->persist($model);
        $this->entityManager->flush();
    }

}