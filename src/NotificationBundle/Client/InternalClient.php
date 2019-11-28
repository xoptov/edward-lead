<?php

namespace NotificationBundle\Client;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use NotificationBundle\Entity\Notification;
use NotificationBundle\Exception\ValidationNotificationClientException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class InternalClient
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * InternalClient constructor.
     *
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * @param Notification $model
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ValidationNotificationClientException
     */
    public function send(Notification $model): void
    {
        $this->validate($model);

        $this->entityManager->persist($model);
        $this->entityManager->flush();
    }

    /**
     * @param Notification $model
     *
     * @throws ValidationNotificationClientException
     */
    public function validate(Notification $model)
    {
        $errors = $this->validator->validate($model);

        if (count($errors) > 0) {
            throw new ValidationNotificationClientException($errors);
        }
    }

}