<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Room;
use AppBundle\Entity\Member;
use AppBundle\Entity\User;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class UserRepository extends EntityRepository
{
    /**
     * @param Room $room
     *
     * @return User[]
     */
    public function getUsersInRoom(Room $room): array
    {
        $queryBuilder = $this->createQueryBuilder('u');

        $query = $queryBuilder
            ->innerJoin(Member::class, 'm', Join::WITH, 'u = m.user AND m.room = :room')
                ->setParameter('room', $room)
            ->where('u.typeSelected = :type_selected')
                ->setParameter('type_selected', true)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param Room $room
     *
     * @return User[]
     */
    public function getBuyersInRoom(Room $room): array
    {
        $queryBuilder = $this->createQueryBuilder('u');

        $query = $queryBuilder
            ->innerJoin(Member::class, 'm', Join::WITH, 'u = m.user AND m.room = :room')
                ->setParameter('room', $room)
            ->where('u.typeSelected = :type_selected')
                ->setParameter('type_selected', true)
            ->andWhere('u.roles LIKE :role_company')
                ->setParameter('role_company', '%ROLE_COMPANY%')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return array
     */
    public function getAdmins(): array
    {
        $queryBuilder = $this->createQueryBuilder('u');

        $query = $queryBuilder->where('u.roles LIKE \'%ROLE%ADMIN%\'')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param string $accessToken
     * @return null|string
     *
     * @throws NonUniqueResultException
     */
    public function getUsernameByAccessToken(string $accessToken): ?string
    {
        $qb = $this->createQueryBuilder('u');

        $query = $qb->select('u.email')
            ->where('u.token = :token')
            ->setParameter('token', $accessToken)
            ->setMaxResults(1)
            ->getQuery();

        return $query->getSingleScalarResult();
    }
}
