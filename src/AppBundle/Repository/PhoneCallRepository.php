<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Trade;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

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
}