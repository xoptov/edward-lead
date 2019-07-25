<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Room;
use AppBundle\Entity\Member;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * @param Room $room
     *
     * @return array
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
}
