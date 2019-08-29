<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Lead;
use AppBundle\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class LeadRepository extends EntityRepository
{
    /**
     * @param User $user
     * @param string $status
     *
     * @return int
     *
     * @throws NonUniqueResultException
     */
    public function getOwnCount(User $user): int
    {
        $queryBuilder = $this->createQueryBuilder('l');

        $queryBuilder
            ->select('count(l.id)')
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
    public function getCountAddedByDate(\DateTime $compareDate): int
    {
        $queryBuilder = $this->createQueryBuilder('l');

        $query = $queryBuilder
            ->select('count(l.id)')
            ->where('l.createdAt BETWEEN :from AND :to')
                ->setParameter('from', $compareDate->format('Y-m-d 00:00:00'))
                ->setParameter('to', $compareDate->format('Y-m-d 23:59:59'))
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
            ->select('count(l.id)');

        $this->addStatusesCondition($queryBuilder, [$status]);

        $query = $queryBuilder->getQuery();

        return $query->getSingleScalarResult();
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


}