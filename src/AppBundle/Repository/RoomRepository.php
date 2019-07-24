<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use AppBundle\Entity\Member;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityRepository;

class RoomRepository extends EntityRepository
{
    /**
     * @param User $user
     *
     * @return mixed
     */
    public function getByMember(User $user)
    {
        $qb = $this->createQueryBuilder('r');
        $query = $qb
            ->join(Member::class, 'm', Join::ON, 'r = m.room AND m.user = :user')
                ->setParameter('user', $user)
            ->where('m.enabled = :enabled')
                ->setParameter('enabled', true)
            ->getQuery();

        return $query->getResult();
    }
}