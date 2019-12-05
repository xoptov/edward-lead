<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Lead;
use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class LeadRepository extends EntityRepository
{
    /**
     * @param User $user
     *
     * @return int
     *
     * @throws NonUniqueResultException
     */
    public function getOwnCount(User $user): int
    {
        $queryBuilder = $this->createQueryBuilder('l');

        $queryBuilder
            ->select('COUNT(l.id)')
            ->where('l.user = :user')
            ->setParameter('user', $user);

        $this->addStatusesCondition($queryBuilder, [Lead::STATUS_EXPECT]);

        $query = $queryBuilder->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * @param array $rooms
     * @param array $statuses
     *
     * @return Lead[]
     */
    public function getOffersByRooms(array $rooms, array $statuses): array
    {
        $queryBuilder = $this->createQueryBuilder('l')
            ->orderBy('l.status', 'ASC')
            ->addOrderBy('l.updatedAt', 'DESC')
            ->addOrderBy('l.createdAt', 'DESC');

        $this->addRoomsCondition($queryBuilder, $rooms);
        $this->addStatusesCondition($queryBuilder, $statuses);

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    /**
     * @param array $cities
     * @param array $statuses
     *
     * @return Lead[]
     */
    public function getOffersByCities(array $cities, array $statuses): array
    {
        $queryBuilder = $this->createQueryBuilder('l')
            ->where('l.room IS NULL')
            ->orderBy('l.updatedAt', 'DESC')
            ->addOrderBy('l.createdAt', 'DESC');

        $this->addCitiesCondition($queryBuilder, $cities);
        $this->addStatusesCondition($queryBuilder, $statuses);

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    /**
     * @param array $statuses
     *
     * @return Lead[]
     */
    public function getOffers(array $statuses): array
    {
        $queryBuilder = $this->createQueryBuilder('l')
            ->where('l.room IS NULL')
            ->orderBy('l.updatedAt', 'DESC')
            ->addOrderBy('l.createdAt', 'DESC');

        $this->addStatusesCondition($queryBuilder, $statuses);

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    /**
     * @param User $buyer
     *
     * @return Lead|null
     *
     * @throws NonUniqueResultException
     */
    public function getInWorkByBuyer(User $buyer): ?Lead
    {
        $queryBuilder = $this->createQueryBuilder('l');

        $queryBuilder
            ->innerJoin('l.trade', 't')
            ->where('t.buyer = :buyer')
                ->setParameter('buyer', $buyer)
            ->orderBy('l.updatedAt', 'DESC')
            ->setMaxResults(1);

        $this->addStatusesCondition($queryBuilder, [Lead::STATUS_IN_WORK]);

        $query = $queryBuilder->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * @param \DateTime $compareDate
     *
     * @return int
     *
     * @throws NonUniqueResultException
     */
    public function getAddedCountByDate(\DateTime $compareDate): int
    {
        $queryBuilder = $this->getAddedCountByDateQueryBuilder($compareDate);
        $query = $queryBuilder->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * @param Room      $room
     * @param \DateTime $compareDate
     *
     * @return int
     *
     * @throws NonUniqueResultException
     */
    public function getAddedCountInRoomByDate(Room $room, \DateTime $compareDate): int
    {
        $queryBuilder = $this->getAddedCountByDateQueryBuilder($compareDate);
        $query = $queryBuilder
            ->andWhere('l.room = :room')
                ->setParameter('room', $room)
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * @param array     $rooms
     * @param \DateTime $compareDate
     *
     * @return array
     */
    public function getAddedInRoomsByDate(array $rooms, \DateTime $compareDate): array
    {
        $queryBuilder = $this->createQueryBuilder('l');

        $query = $queryBuilder
            ->innerJoin('l.room', 'r', Join::WITH, 'r IN (:rooms) AND r.enabled = :enabled')
                ->setParameter('rooms', $rooms)
                ->setParameter('enabled', true)
            ->where('l.createdAt BETWEEN :from AND :to')
                ->setParameter('from', $compareDate->format('Y-m-d 00:00:00'))
                ->setParameter('to', $compareDate->format('Y-m-d 23:59:59'))
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param string $status
     *
     * @return int
     *
     * @throws NonUniqueResultException
     */
    public function getCountByStatus(string $status): int
    {
        $queryBuilder = $this->createQueryBuilder('l')
            ->select('COUNT(l.id)');

        $this->addStatusesCondition($queryBuilder, [$status]);

        $query = $queryBuilder->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * @param string    $phone
     * @param Room|null $room
     *
     * @return array
     */
    public function getByPhoneAndWithNoFinishStatus(string $phone, ?Room $room = null): array
    {
        $queryBuilder = $this->createQueryBuilder('l');

        $queryBuilder
            ->where('l.phone = :phone')
                ->setParameter('phone', $phone)
            ->andWhere('l.status IN (:statuses)')
                ->setParameter('statuses', [Lead::STATUS_EXPECT, Lead::STATUS_IN_WORK, Lead::STATUS_ARBITRATION]);

        if ($room) {
            $queryBuilder->andWhere('l.room = :room')
                ->setParameter('room', $room);
        }

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    /**
     * @param string    $phone
     * @param Room|null $room
     * @param int       $tradePeriod
     *
     * @return int
     *
     * @throws DBALException
     */
    public function getCountByPhoneAndWithNoFinishStatusOrTradePeriod(
        string $phone,
        ?Room $room = null,
        int $tradePeriod = 30
    ): int {

        $conn = $this->getEntityManager()->getConnection();

        $query = <<<SQL
SELECT COUNT(l.id)
FROM lead AS l
LEFT JOIN trade AS t ON l.id = t.lead_id
LEFT JOIN operation AS o ON t.id = o.id
WHERE l.phone = :phone
  AND (l.status IN (:status_expect, :status_in_work, :status_arbitration)
    OR (o.id IS NOT NULL AND DATE(o.created_at) > :expire_date)
  )
SQL;

        if ($room) {
            $query .= ' AND l.room_id = :room_id';
        } else {
            $query .= ' AND l.room_id IS NULL';
        }

        $expireDate = new \DateTime('-' . $tradePeriod . ' days');

        $stmt = $conn->prepare($query);
        $stmt->bindValue('phone', $phone);
        $stmt->bindValue('status_expect', Lead::STATUS_EXPECT);
        $stmt->bindValue('status_in_work', Lead::STATUS_IN_WORK);
        $stmt->bindValue('status_arbitration', Lead::STATUS_ARBITRATION);
        $stmt->bindValue('expire_date', $expireDate->format('Y-m-d'));

        if ($room) {
            $stmt->bindValue('room_id', $room->getId());
        }

        if($stmt->execute()) {
            return $stmt->fetchColumn();
        }

        return 0;
    }

    /**
     * @param \DateTime $timeBound
     *
     * @return Lead[]
     */
    public function getByEndedTimerAndExpect(\DateTime $timeBound): array
    {
        $queryBuilder = $this->createQueryBuilder('l');

        $query = $queryBuilder
            ->innerJoin('l.room', 'r', Join::WITH, 'r.enabled = :enabled')
                ->setParameter('enabled', true)
            ->where('l.status = :status')
                ->setParameter('status', Lead::STATUS_EXPECT)
            ->andWhere('l.timer.endAt IS NOT NULL')
            ->andWhere('l.timer.endAt <= :time_bound')
                ->setParameter('time_bound', $timeBound)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param \DateTime $compareDate
     *
     * @return QueryBuilder
     */
    private function getAddedCountByDateQueryBuilder(\DateTime $compareDate): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('l');

        $queryBuilder
            ->select('COUNT(l.id)')
            ->where('l.createdAt BETWEEN :from AND :to')
                ->setParameter('from', $compareDate->format('Y-m-d 00:00:00'))
                ->setParameter('to', $compareDate->format('Y-m-d 23:59:59'));

        return $queryBuilder;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $rooms
     */
    private function addRoomsCondition(QueryBuilder $queryBuilder, array $rooms): void
    {
        $queryBuilder
            ->andWhere('l.room IN (:rooms)')
            ->setParameter('rooms', $rooms);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $cities
     */
    private function addCitiesCondition(QueryBuilder $queryBuilder, array $cities): void
    {
        $queryBuilder
            ->andWhere('l.city IN (:cities)')
            ->setParameter('cities', $cities);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $statuses
     */
    private function addStatusesCondition(QueryBuilder $queryBuilder, array $statuses)
    {
        $queryBuilder
            ->andWhere('l.status IN (:statuses)')
            ->setParameter('statuses', $statuses);
    }

    /**
     * @param User $user
     * 
     * @return array
     */
    public function getCountByUserLastMonth(User $user):array
    {
        $conn = $this->getEntityManager()->getConnection();
        $userId = $user->getId();

        $sql =  <<< SQL
        SELECT ( SELECT COUNT(l1.id) cnt FROM lead l1 WHERE l1.user_id=:userId 
        AND created_at >= DATE( DATE_SUB(CURDATE(),INTERVAL 1 MONTH)) ) 'total',
        ( SELECT COUNT(l2.id) cnt FROM lead l2 WHERE l2.user_id=:userId 
        AND created_at >= DATE( DATE_SUB( CURDATE(),INTERVAL 1 MONTH ) ) AND status="target" ) 'target',
        ( SELECT COUNT(l3.id) cnt FROM lead l3 WHERE l3.user_id=:userId 
        AND created_at >= DATE( DATE_SUB( CURDATE(),INTERVAL 1 MONTH ) ) AND status="in_work" ) 'in_work',
        ( SELECT COUNT(l4.id) cnt FROM lead l4 WHERE l4.user_id=:userId 
        AND created_at >= DATE( DATE_SUB( CURDATE(),INTERVAL 1 MONTH ) ) AND status="not_target" ) 'not_target';
SQL;

        $stmt = $conn->prepare($sql);
        $stmt->bindValue( 'userId', $user->getId() );

        if ($stmt->execute() && $stmt->rowCount()) {
            return $stmt->fetch();
        }

        return array();
    }

    /**
     * @param User $user
     * 
     * @return array
     */
    public function getCountByUserLastDay(User $user):array
    {
        $conn = $this->getEntityManager()->getConnection();
        $userId = $user->getId();

        $sql =  <<< SQL
        SELECT ( SELECT COUNT(l1.id) cnt FROM lead l1 WHERE l1.user_id = :userId 
        AND created_at >= DATE( DATE_SUB(CURDATE(), INTERVAL 24 HOUR ) ) ) 'total',
        ( SELECT COUNT(l2.id) cnt FROM lead l2 WHERE l2.user_id = :userId 
        AND created_at >= DATE( DATE_SUB( CURDATE(), INTERVAL 24 HOUR ) ) AND status="target" ) 'target',
        ( SELECT COUNT(l3.id) cnt FROM lead l3 WHERE l3.user_id = :userId 
        AND created_at >= DATE( DATE_SUB( CURDATE(), INTERVAL 24 HOUR ) ) AND status="in_work" ) 'in_work',
        ( SELECT COUNT(l4.id) cnt FROM lead l4 WHERE l4.user_id = :userId 
        AND created_at >= DATE( DATE_SUB( CURDATE(), INTERVAL 24 HOUR ) ) AND status="not_target" ) 'not_target';
SQL;

        $stmt = $conn->prepare($sql);
        $stmt->bindValue( 'userId', $user->getId() );

        if ($stmt->execute() && $stmt->rowCount()) {
            return $stmt->fetch();
        }

        return array();
    }
}
