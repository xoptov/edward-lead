<?php

namespace AppBundle\Repository;

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
}