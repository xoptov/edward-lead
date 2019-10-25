<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Lead;
use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use AppBundle\Entity\Member;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;

class RoomRepository extends EntityRepository
{
    /**
     * @param User   $user
     * @param string $orderBy
     * @param string $direction
     *
     * @return Room[]
     */
    public function getByMember(User $user, string $orderBy = 'enabled', string $direction = 'DESC'): array
    {
        $qb = $this->createQueryBuilder('r');

        $query = $qb
            ->join(Member::class, 'm', Join::WITH, 'r = m.room AND m.user = :user')
                ->setParameter('user', $user)
            ->orderBy('r.' . $orderBy, $direction)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param array $leads
     *
     * @return Room[]
     */
    public function getByLeads(array $leads): array
    {
        $qb = $this->createQueryBuilder('r');

        $query = $qb
            ->join(Lead::class, 'l', Join::WITH, 'l.room = r')
            ->where('l IN (:leads)')
                ->setParameter('leads', $leads)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param string $inviteTokenSuffix
     *
     * @return Room
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getByInviteShortToken(string $inviteTokenSuffix): Room
    {
        $qb = $this->createQueryBuilder('r');
        $query = $qb
            ->where('r.inviteToken LIKE :invite_token_suffix')
                ->setParameter('invite_token_suffix', '%' . $inviteTokenSuffix)
            ->setMaxResults(1)
            ->getQuery();

        return $query->getSingleResult();
    }
}