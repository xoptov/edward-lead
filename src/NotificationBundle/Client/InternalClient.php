<?php

namespace NotificationBundle\Client;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use NotificationBundle\Entity\Notification;
use NotificationBundle\Exception\ValidationNotificationClientException;
use Psr\Log\LoggerInterface;
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * InternalClient constructor.
     *
     * @param ValidatorInterface     $validator
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface        $logger
     */
    public function __construct(
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    )
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->logger = $logger;
    }

    /**
     * @param Notification $model *
     */
    public function send(Notification $model): void
    {
        try {

            $this->validate($model);
            $this->entityManager->persist($model);
            $this->entityManager->flush();

        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
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