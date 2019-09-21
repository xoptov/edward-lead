<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Lead;
use AppBundle\Entity\User;
use AppBundle\Entity\Trade;
use AppBundle\Entity\PhoneCall;
use Doctrine\ORM\Query\Expr\Join;
use AppBundle\Entity\PBX\Callback;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class PhoneCallRepository extends EntityRepository
{
    /**
     * @param User  $user
     * @param array $trades
     *
     * @return array|null
     */
    public function getCallsWithTrades(User $user, array $trades): ?array
    {
        if (empty($trades)) {
            return null;
        }

        $ids = array_map(function(Trade $trade){
            return $trade->getId();
        }, $trades);

        $queryBuilder = $this->createQueryBuilder('pc');

        $query = $queryBuilder
            ->where("pc.trade IN (:ids)")
                ->setParameter("ids", $ids)
            ->andWhere("pc.caller = :user")
                ->setParameter("user", $user)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param Lead $lead
     * @param User $caller
     *
     * @return PhoneCall|null
     *
     * @throws NonUniqueResultException
     */
    public function getAnsweredPhoneCallByLeadAndCaller(Lead $lead, User $caller): ?PhoneCall
    {
        $qb = $this->createQueryBuilder('pc');

        $query = $qb
            ->join('pc.callbacks', 'cb', Join::WITH, 'cb.status = :success')
                ->setParameter('success', Callback::STATUS_SUCCESS)
            ->where('pc.trade = :trade')
                ->setParameter('trade', $lead->getTrade())
            ->andWhere('pc.caller = :caller')
                ->setParameter('caller', $caller)
            ->andWhere('pc.status = :processed')
                ->setParameter('processed', PhoneCall::STATUS_PROCESSED)
            ->setMaxResults(1)
            ->getQuery();

        return $query->getOneOrNullResult();
    }
}