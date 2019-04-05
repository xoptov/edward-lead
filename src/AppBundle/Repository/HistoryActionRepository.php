<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class HistoryActionRepository extends EntityRepository
{
    /**
     * @param User $user
     *
     * @return array
     */
    public function getByUserAndActionInDescOrder(User $user, string $action)
    {
        $qb = $this->createQueryBuilder('ha');
        $query = $qb
            ->where('ha.user = :user')
                ->setParameter('user', $user)
            ->andWhere('ha.action = :action')
                ->setParameter('action', $action)
            ->orderBy('ha.happenedAt', 'DESC')
            ->getQuery();

        return $query->getResult();
    }
}