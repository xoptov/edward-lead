<?php

namespace NotificationBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use NotificationBundle\Entity\NotificationConfiguration;

class NotificationConfigurationRepository extends ServiceEntityRepository
{
    /**
     * NotificationConfigurationRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationConfiguration::class);
    }

    /**
     * @param string $case
     * @param string $channel
     * @param User   $user
     *
     * @return object|null
     */
    public function findByConfigurations(string $case, string $channel, User $user)
    {
        return $this->findOneBy(
            [
                "case" => $case,
                "channel" => $channel,
                "user" => $user
            ]
        );
    }
}