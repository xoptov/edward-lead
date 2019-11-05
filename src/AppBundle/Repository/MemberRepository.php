<?php

namespace AppBundle\Repository;

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
}