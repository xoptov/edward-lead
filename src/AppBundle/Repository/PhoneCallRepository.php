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
     * @param User $user
     * @param $trades
     * @return array|null
     */
    public function getCallsWithTrades(User $user, $trades): ?array
    {
        if ( ! is_array($trades)) {
            return null;
        }

        if ( ! sizeof($trades)) {
            return null;
        }

        /**
         * @var Trade $trade
         * @return int
         */
        $leadId = function ($trade) {
            return $trade->getLead()->getId();
        };

        $leadsId = [];

        foreach ($trades as $trade) {
            $leadsId[] = $leadId($trade);
        }

        if (sizeof($leadsId) == 0) {
            return null;
        }

        $queryBuilder = $this->createQueryBuilder('pc');

        $query = $queryBuilder
            ->where("pc.lead IN (:ids)")
                ->setParameter("ids", $leadsId)
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