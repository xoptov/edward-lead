<?php

namespace AppBundle\Security;

use Psr\Log\LoggerInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\HttpUtils;
use AppBundle\Exception\TemporarilyBannedException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;

class AuthenticationFailureHandler extends DefaultAuthenticationFailureHandler
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection          $connection
     * @param HttpKernelInterface $httpKernel
     * @param HttpUtils           $httpUtils
     * @param LoggerInterface     $logger
     * @param array               $options
     */
    public function __construct(
        Connection $connection,
        HttpKernelInterface $httpKernel, 
        HttpUtils $httpUtils,
        LoggerInterface $logger = null,
        array $options = []
    ) {
        parent::__construct($httpKernel, $httpUtils, $options, $logger);

        $this->connection = $connection;
    }

    /**
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @throws DBALException
     *
     * @return Response
     */
    public function onAuthenticationFailure(
        Request $request, 
        AuthenticationException $exception
    ) {
        $sql = 'INSERT INTO authentication_failure(ip_address, created_at) VALUES(:ip_address, NOW())';

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('ip_address', ip2long($request->getClientIp()));

        if (!$stmt->execute()) {
            $this->logger->error($stmt->errorInfo());
        }

        $response = parent::onAuthenticationFailure($request, $exception);

        //todo: Это конечно хак, но я пока незнаю как лучше.
        if ($exception instanceof TemporarilyBannedException) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }

        return $response;
    }
}