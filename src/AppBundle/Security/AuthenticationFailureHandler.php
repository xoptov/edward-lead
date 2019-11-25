<?php

namespace AppBundle\Security;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\AuthenticationFailure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;

class AuthenticationFailureHandler extends DefaultAuthenticationFailureHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var int
     */
    private $timeFrame;

    /**
     * @var int
     */
    private $failRetry;

    /**
     * @param EntityManagerInterface $entityManager
     * @param HttpKernelInterface    $httpKernel
     * @param HttpUtil               $httpUtils
     * @param LoggerInterface        $logger
     * @param array                  $options
     * @param int                    $timeFrame
     * @param int                    $failRetry
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        HttpKernelInterface $httpKernel, 
        HttpUtils $httpUtils,
        int $timeFrame,
        int $failRetry,
        LoggerInterface $logger = null,
        array $options = []
    ) {
        parent::__construct($httpKernel, $httpUtils, $options, $logger);

        $this->entityManager = $entityManager;
        $this->timeFrame = $timeFrame;
        $this->failRetry = $failRetry;
    }

    /**
     * @param Request                 $request
     * @param AuthenticationException $exception
     */
    public function onAuthenticationFailure(
        Request $request, 
        AuthenticationException $exception
    ) {
        $clientIp = ip2long($request->getClientIp());

        $authFailure = new AuthenticationFailure($clientIp);

        $this->entityManager->persist($authFailure);
        $this->entityManager->flush();

        $failRetryCount = $this->entityManager
            ->getRepository(AuthenticationFailure::class)
            ->getCountByIPAndSecureTimeFrame($clientIp, $this->timeFrame);

        if ($failRetryCount >= $this->failRetry) {
            //todo: ну тут по идеи ещё нужно что-то делать с блокировкой пользователя!!!
            
            return $this->httpUtils->createRedirectResponse($request, 'app_banned');
        }

        return parent::onAuthenticationFailure($request, $exception);
    }
}