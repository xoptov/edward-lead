<?php

namespace AppBundle\Repository;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityRepository;

class TradeRepository extends EntityRepository
{
    /**
     * @param array  $cities
     * @param int    $status
     * @param string $order
     *
     * @return array
     */
    public function getByCitiesAndStatus(array $cities, int $status, string $order = 'DESC'): array
    {
        $qb = $this->createQueryBuilder('t');
        $query = $qb
            ->join('t.lead', 'l', Join::WITH, 'l.city IN (:cities)')
                ->setParameter('cities', $cities)
            ->andWhere('t.status = :status')
                ->setParameter('status', $status)
            ->orderBy('t.createdAt', $order)
            ->getQuery();

        return $query->getResult();
    }
}