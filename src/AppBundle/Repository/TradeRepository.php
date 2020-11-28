<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Lead;
use AppBundle\Entity\Trade;
use AppBundle\Entity\User;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;

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
    public function getByWarrantyAndIncomplete(): array
    {
        $qb = $this->createWarrantyAndStatusesQueryBuilder([Trade::STATUS_NEW, Trade::STATUS_CALL_BACK]);
        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * @param User $buyer
     *
     * @return Trade[]
     */
    public function getByBuyerAndWarrantyAndIncomplete(User $buyer): array
    {
        $qb = $this->createWarrantyAndStatusesQueryBuilder([Trade::STATUS_NEW, Trade::STATUS_CALL_BACK]);
        $query = $qb
            ->andWhere('t.buyer = :buyer')
                ->setParameter('buyer', $buyer)
            ->orderBy('t.createdAt', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param User $user
     *
     * @return int
     *
     * @throws DBALException
     */
    public function getAmountInPendingTrades(User $user): int
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = <<<SQL
SELECT SUM(o.amount)
FROM operation AS o
INNER JOIN trade AS t ON o.id = t.id AND t.seller_id = :seller_id
WHERE t.status IN (0,3,4);
SQL;

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('seller_id', $user->getId());

        if ($stmt->execute() && $stmt->rowCount()) {
            $value = $stmt->fetchColumn();
            if (is_null($value)) {
                return 0;
            }
            return $value;
        }

        return 0;
    }

    /**
     * @param array $statuses
     *
     * @return QueryBuilder
     */
    private function createWarrantyAndStatusesQueryBuilder(array $statuses): QueryBuilder
    {
        $qb = $this->createQueryBuilder('t');

        $qb->join('t.lead', 'l')
            ->leftJoin('l.room', 'r')
            ->where('r IS NULL OR r.platformWarranty = :warranty')
                ->setParameter('warranty', true)
            ->andWhere('t.status IN (:statuses)')
                ->setParameter('statuses', $statuses);

        return $qb;
    }
}