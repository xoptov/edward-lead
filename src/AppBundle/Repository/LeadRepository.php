<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Lead;
use AppBundle\Entity\User;
use Doctrine\ORM\Query\Expr\Join;
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

    /**
     * @param User $user
     *
     * @return Lead|null
     *
     * @throws NonUniqueResultException
     */
    public function getByUserAndReserved(User $user): ?Lead
    {
        $queryBuilder = $this->createQueryBuilder('l');

        $query = $queryBuilder
            ->innerJoin('l.trade', 't')
            ->where('t.buyer = :buyer')
                ->setParameter('buyer', $user)
            ->andWhere('l.status = :reserved')
                ->setParameter('reserved', Lead::STATUS_RESERVED)
            ->orderBy('l.updatedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        return $query->getOneOrNullResult();
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
     * @param array $rooms
     *
     * @return array
     */
    public function getReservedInRooms(array $rooms): array
    {
        $queryBuilder = $this->createQueryBuilder('l');

        $query = $queryBuilder
            ->where('l.room IN (:rooms)')
                ->setParameter('rooms', $rooms)
            ->getQuery();

        return $query->getResult();
    }
}