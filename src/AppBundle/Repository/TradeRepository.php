<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Lead;
use AppBundle\Entity\Trade;
use AppBundle\Entity\User;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class TradeRepository extends EntityRepository
{
    /**
     * @param array  $cities
     * @param int    $status
     * @param string $order
     *
     * @return Trade[]
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

    /**
     * @param Lead    $lead
     * @param User    $buyer
     * @param integer $status
     *
     * @return Trade|null
     *
     * @throws NonUniqueResultException
     */
    public function getByLeadAndBuyerAndStatus(Lead $lead, User $buyer, int $status): ?Trade
    {
        $qb = $this->createQueryBuilder('t');
        $query = $qb
            ->where('t.buyer = :buyer')
                ->setParameter('buyer', $buyer)
            ->andWhere('t.lead = :lead')
                ->setParameter('lead', $lead)
            ->andWhere('t.status = :status')
                ->setParameter('status', $status)
            ->setMaxResults(1)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * @return Trade[]
     */
    public function getWithWarrantyAndIncomplete(): array
    {
        $qb = $this->createQueryBuilder('t');
        $query = $qb
            ->leftJoin('t.room', 'r')
            ->where('r IS NULL OR r.platformWarranty = :warranty')
                ->setParameter('warranty', true)
            ->andWhere('t.status IN :statuses')
                ->setParameter('statuses', [Trade::STATUS_NEW, Trade::STATUS_CALL_BACK])
            ->getQuery();

        return $query->getResult();
    }
}