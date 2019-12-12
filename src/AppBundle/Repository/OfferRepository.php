<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;

class OfferRepository extends EntityRepository
{
    /**
     * @param User $user
     * @param int  $seconds
     *
     * @return int
     * 
     * @throws DBALException
     */
    public function getCountByUserInInterval(User $user, int $seconds): int
    {
        $sql = <<<SQL
SELECT COUNT(id) 
FROM offer_request 
WHERE user_id = :user_id
AND UNIX_TIMESTAMP(created_at) + :seconds >= UNIX_TIMESTAMP(NOW());
SQL;

        $conn = $this->getEntityManager()->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('user_id', $user->getId());
        $stmt->bindValue('seconds', $seconds);

        if ($stmt->execute()) {
            return $stmt->fetchColumn();
        }

        return 0;
    }
}