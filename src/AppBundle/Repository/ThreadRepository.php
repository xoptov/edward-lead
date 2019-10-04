<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use AppBundle\Entity\Thread;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class ThreadRepository extends EntityRepository
{
    /**
     * @param User      $creator
     * @param \DateTime $timeBound
     *
     * @return Thread|null
     *
     * @throws NonUniqueResultException
     */
    public function getLastSupportThreadByCreatorAndTimeBound(User $creator, \DateTime $timeBound): ?Thread
    {
        $qb = $this->createQueryBuilder('t');

        $query = $qb
            ->where('t.createdBy = :creator')
                ->setParameter('creator', $creator)
            ->andWhere('t.typeAppeal = :type_appeal')
                ->setParameter('type_appeal', Thread::TYPE_SUPPORT)
            ->andWhere('t.createdAt >= :time_bound')
                ->setParameter('time_bound', $timeBound)
            ->setMaxResults(1)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery();

        return $query->getOneOrNullResult();
    }
}