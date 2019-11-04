<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use AppBundle\Entity\Member;
use Doctrine\ORM\QueryBuilder;
use Doctrine\DBAL\DBALException;
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
    public function getAdvertisersInRoom(Room $room): array
    {
        $queryBuilder = $this->getAdvertisersInRoomsQueryBuilder([$room]);

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    /**
     * @param Room[] $rooms
     *
     * @return User[]
     */
    public function getAdvertisesInRooms(array $rooms): array
    {
        $queryBuilder = $this->getAdvertisersInRoomsQueryBuilder($rooms);

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    /**
     * @param Member[] $members
     *
     * @return User[]
     */
    public function getByMembers(array $members): array
    {
        $queryBuilder = $this->createQueryBuilder('u');

        $query = $queryBuilder
            ->join(Member::class, 'm', Join::WITH, 'u = m.user AND m IN (:members)')
                ->setParameter('members', $members)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param Room[] $rooms
     *
     * @return QueryBuilder
     */
    private function getAdvertisersInRoomsQueryBuilder(array $rooms): QueryBuilder
    {
       $queryBuilder = $this->createQueryBuilder('u');

       $queryBuilder
           ->join(Member::class, 'm', Join::WITH, 'u = m.user AND m.room IN (:rooms)')
                ->setParameter('rooms', $rooms)
           ->where('u.typeSelected = :type_selected')
                ->setParameter('type_selected', true)
           ->andWhere('u.roles LIKE :role_company')
                ->setParameter('role_company', '%ROLE_COMPANY%');

       return $queryBuilder;
    }

    /**
     * @return array
     */
    public function getAdmins(): array
    {
        $queryBuilder = $this->createQueryBuilder('u');

        $query = $queryBuilder
            ->where('u.roles LIKE \'%ROLE%ADMIN%\'')
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
        $queryBuilder = $this->createQueryBuilder('u');

        $query = $queryBuilder
            ->select('u.email')
            ->where('u.token = :token')
                ->setParameter('token', $accessToken)
            ->setMaxResults(1)
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * @param string $referrerToken
     *
     * @return User|null
     *
     * @throws DBALException
     */
    public function getByReferrerToken(string $referrerToken): ?User
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT id FROM user WHERE SHA1(id) LIKE :hash_suffix LIMIT 1';

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('hash_suffix', '%' . $referrerToken);

        if ($stmt->execute() && $stmt->rowCount()) {

            $userId = $stmt->fetchColumn();

            if (!$userId) {
                return null;
            }

            /** @var User $user */
            $user = $this->find($userId);

            return $user;
        }

        return null;
    }

    /**
     * @param User $user
     *
     * @return int
     *
     * @throws DBALException
     */
    public function getReferralCount(User $user): int
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT COUNT(id) FROM user WHERE referrer_id = :referrer_id';

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('referrer_id', $user->getId());

        if ($stmt->execute() && $stmt->rowCount()) {
            return $stmt->fetchColumn();
        }

        return 0;
    }
}
