<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AuthenticationFailureRepository extends EntityRepository
{
    /**
     * @param int $ip
     * @param int $seconds
     * 
     * @return int
     */
    public function getCountByIPAndSecureTimeFrame(int $ip, int $seconds): int
    {
        $conn = $this->getEntityManager()->getConnection();

        $query = <<<SQL
SELECT COUNT(id)
FROM authentication_failure
WHERE ip = :ip
AND DATE_FORMAT(created_at, '%Y-%m-%d %H:%m:%s') > :start_time_frame
SQL;

        $startTimeFrame = new \DateTime('-' . $seconds . 'seconds');

        $stmt = $conn->prepare($query);
        $stmt->bindValue('ip', $ip);
        $stmt->bindValue('start_time_frame', $startTimeFrame->format('Y-m-d H:i:s'));

        if ($stmt->execute()) {
            return $stmt->fetchColumn();
        }

        return 0;
    }
}