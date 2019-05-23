<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Invoice;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class InvoiceRepository extends EntityRepository
{
    /**
     * @param User $user
     * @param null|string $order
     *
     * @return Invoice[]
     */
    public function getAllByUser(User $user, ?string $order = 'DESC')
    {
        $qb = $this->createQueryBuilder('i');
        $query = $qb->where('i.user = :user')
            ->setParameter('user', $user)
            ->orderBy('i.id', $order)
            ->getQuery();

        return $query->getResult();
    }
}