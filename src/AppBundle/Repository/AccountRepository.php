<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Account;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;

class AccountRepository extends EntityRepository
{
    /**
     * @return Account
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getFeesAccount(): Account
    {
        $qb = $this->createQueryBuilder('a');
        $query = $qb
            ->where('a.enabled = :enabled')
                ->setParameter('enabled', true)
            ->andWhere('a.description LIKE :description')
                ->setParameter('description', '%для%комиссий%')
            ->setMaxResults(1)
            ->getQuery();

        return $query->getSingleResult();
    }

    /**
     * @return Account
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getTelephonyAccount(): Account
    {
        $qb = $this->createQueryBuilder('a');
        $query = $qb
            ->where('a.enabled = :enabled')
                ->setParameter('enabled', true)
            ->andWhere('a.description LIKE :description')
                ->setParameter('description', '%для%телефонии%')
            ->setMaxResults(1)
            ->getQuery();

        return $query->getSingleResult();
    }
}