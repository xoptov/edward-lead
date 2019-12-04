<?php

namespace AppBundle\Security;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Symfony\Component\HttpFoundation\RequestStack;
use AppBundle\Exception\TemporarilyBannedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

class BannedChecker implements UserCheckerInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var int
     */
    private $timeFrame;

    /**
     * @var int
     */
    private $retryLimit;

    /**
     * @param RequestStack $requestStack
     * @param Connection   $connection
     * @param int          $timeFrame
     * @param int          $retryLimit
     */
    public function __construct(
        RequestStack $requestStack,
        Connection $connection,
        int $timeFrame,
        int $retryLimit
    ) {
        $this->requestStack = $requestStack;
        $this->connection = $connection;
        $this->timeFrame = $timeFrame;
        $this->retryLimit = $retryLimit;
    }

    /**
     * @param UserInterface $user
     *
     * @throws DBALException
     */
    public function checkPreAuth(UserInterface $user)
    {
        $query = <<<SQL
SELECT COUNT(id) 
FROM authentication_failure 
WHERE ip_address = :ip_address 
AND DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') > :start_time_frame
SQL;

        $startTimeFrame = new \DateTime('-' . $this->timeFrame . ' seconds');

        $ipAddress = ip2long($this->requestStack->getMasterRequest()->getClientIp());

        $stmt = $this->connection->prepare($query);
        $stmt->bindValue('ip_address', $ipAddress);
        $stmt->bindValue('start_time_frame', $startTimeFrame->format('Y-m-d H:i:s'));

        if ($stmt->execute()) {
            $retryCount = intval($stmt->fetchColumn());
            if ($this->retryLimit <= $retryCount) {
                throw new TemporarilyBannedException(
                    'User temporarily banned.'
                );
            }
        }
    }

    /**
     * @param UserInterface $user
     */
    public function checkPostAuth(UserInterface $user)
    {
        return;
    }
}