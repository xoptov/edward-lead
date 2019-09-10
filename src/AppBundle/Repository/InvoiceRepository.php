<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use AppBundle\Entity\Invoice;
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

    /**
     * @param null|string $hash
     *
     * @return Invoice[]|null
     */
    public function getByHash(?string $hash)
    {
        $qb = $this->createQueryBuilder('i');
        $query = $qb->where('i.hash = :hash')
            ->setParameter('hash', $hash)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param null|int $id_invoice
     *
     * @return Invoice[]|null
     */
    public function getById(?int $id_invoice)
    {
        $qb = $this->createQueryBuilder('i');
        $query = $qb->where('i.id = :id')
            ->setParameter('id', $id_invoice)
            ->getQuery();

        return $query->getResult();
    }
}