<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Member;
use AppBundle\Entity\Room;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;

class MemberRepository extends EntityRepository
{
    /**
     * @param array $rooms
     *
     * @return array
     */
    public function getByRooms(array $rooms): array
    {
        $queryBuilder = $this->createQueryBuilder('m');

        $query = $queryBuilder
            ->where('m.room IN (:rooms)')
                ->setParameter('rooms', $rooms)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param Room $room
     *
     * @return int
     *
     * @throws DBALException
     */
    public function getCountInRoom(Room $room): int
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT COUNT(id) FROM member WHERE room_id = :room_id';

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('room_id', $room->getId());

        if ($stmt->execute() && $stmt->rowCount()) {
            return $stmt->fetchColumn();
        }

        return 0;
    }

    /**
     * @param int $days
     *
     * @return Member[]
     *
     * @throws DBALException
     */
    public function getByRecentRoomVisits(int $days): array
    {
        $sql = <<<SQL
SELECT m.id
FROM member m
INNER JOIN room_visit rv1 ON rv1.id = (
   SELECT rv2.id
   FROM room_visit rv2
   WHERE rv2.user_id = rv1.user_id
     AND rv2.room_id = rv1.room_id
     AND DATE(rv2.visited_at) = DATE_SUB(CURDATE(), INTERVAL :days DAY )
   ORDER BY rv2.visited_at DESC
   LIMIT 1
);
SQL;

        $conn = $this->getEntityManager()->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('days', $days);

        $result = [];

        if ($stmt->execute() && $stmt->rowCount()) {

            $ids = [];

            while ($id = $stmt->fetchColumn()) {
                $ids[] = $id;
            }

            return $this->findBy(['id' => $ids]);
        }

        return $result;
    }
}