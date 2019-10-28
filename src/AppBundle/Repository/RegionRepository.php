<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Region;
use Doctrine\ORM\EntityRepository;

class RegionRepository extends EntityRepository
{
    /**
     * @param array $names
     *
     * @return Region[]
     */
    public function getByNames(array $names): array
    {
        $queryBuilder = $this->createQueryBuilder('r');

        $query = $queryBuilder
            ->where('r.name IN (:regions)')
                ->setParameter('regions', $names)
            ->getQuery();

        return $query->getResult();
    }
}