<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use AppBundle\Entity\Member;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityRepository;

class RoomRepository extends EntityRepository
{
    /**
     * @param User   $user
     * @param string $orderBy
     * @param string $direction
     *
     * @return mixed
     */
    public function getByMember(User $user, string $orderBy = 'enabled', string $direction = 'DESC')
    {
        $qb = $this->createQueryBuilder('r');
        $query = $qb
            ->join(Member::class, 'm', Join::WITH, 'r = m.room AND m.user = :user')
                ->setParameter('user', $user)
            ->orderBy('r.' . $orderBy, $direction)
            ->getQuery();

        return $query->getResult();
    }
}