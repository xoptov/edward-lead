<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Account;
use Doctrine\ORM\EntityRepository;

class AccountRepository extends EntityRepository
{
    /**
     * @return Account
     *
     * @throws \Exception
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
}