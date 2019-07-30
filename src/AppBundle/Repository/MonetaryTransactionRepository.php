<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Account;
use Doctrine\ORM\EntityRepository;

class MonetaryTransactionRepository extends EntityRepository
{
    /**
     * @param Account $account
     *
     * @return array
     */
    public function getIncoming(Account $account): array
    {
        $qb = $this->createQueryBuilder('mt');

        $query = $qb
            ->where('mt.account = :account')
                ->setParameter('account', $account)
            ->andWhere('mt.amount > 0')
            ->andWhere('mt.processed = :processed')
                ->setParameter('processed', true)
            ->orderBy('mt.createdAt', 'DESC')
            ->getQuery();

        return $query->getResult();
    }
}