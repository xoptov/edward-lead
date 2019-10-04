<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;

class MessageRepository extends EntityRepository
{
    /**
     * @param User $sender
     * @param int  $frameInSecond
     *
     * @return int
     *
     * @throws DBALException
     */
    public function getCountInTimeFrameBySender(User $sender, int $frameInSecond): int
    {
        $connection = $this->getEntityManager()->getConnection();

        $stmt = $connection->prepare('
          SELECT COUNT(id)
          FROM message
          WHERE UNIX_TIMESTAMP(created_at) + :time_offset >= UNIX_TIMESTAMP(NOW())
          AND sender_id = :sender_id
        ');

        $stmt->bindValue('time_offset', $frameInSecond);
        $stmt->bindValue('sender_id', $sender->getId());

        if ($stmt->execute()) {
            return (int)$stmt->fetchColumn();
        }

        return 0;
    }
}