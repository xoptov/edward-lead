<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Lead;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;

class LeadRepository extends EntityRepository
{
    /**
     * @param User $user
     *
     * @return int
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getActiveCountByUser(User $user): int
    {
        $queryBuilder = $this->createQueryBuilder('l');

        $query = $queryBuilder
            ->select('count(l.id)')
            ->where('l.status = :status')
                ->setParameter('status', Lead::STATUS_ACTIVE)
            ->andWhere('l.user = :user')
                ->setParameter('user', $user)
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * @param array $cities
     *
     * @return Lead[]
     */
    public function getByActiveAndCities(array $cities): array
    {
        $queryBuilder = $this->createQueryBuilder('l');

        $query = $queryBuilder
            ->where('l.city IN (:cities)')
                ->setParameter('cities', $cities)
            ->andWhere('l.status = :status')
                ->setParameter('status', Lead::STATUS_ACTIVE)
            ->orderBy('l.updatedAt', 'DESC')
            ->addOrderBy('l.createdAt', 'DESC')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return Lead[]
     */
    public function getByActive(): array
    {
        $queryBuilder = $this->createQueryBuilder('l');

        $query = $queryBuilder
            ->where('l.status = :status')
                ->setParameter('status', Lead::STATUS_ACTIVE)
            ->orderBy('l.updatedAt', 'DESC')
            ->addOrderBy('l.createdAt', 'DESC')
            ->getQuery();

        return $query->getResult();
    }
}