<?php

namespace AppBundle\Notifications;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

abstract class BaseNotificationContainer
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * BaseNotificationContainer constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     */
    protected function getNotificationOperators()
    {
        /** @var UserRepository $repository */
        $repository = $this->entityManager->getRepository(User::class);

        return $repository->getByRole(User::ROLE_NOTIFICATION_OPERATOR);
    }
}