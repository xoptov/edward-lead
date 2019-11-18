<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Entity\User;
use Psr\Log\LoggerInterface;
use AppBundle\Service\UserManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/api/v1")
 */
class UserController extends Controller
{
    /**
     * @Route("/user/renew-token", name="api_v1_user_renew_token", methods={"GET"}, defaults={"_format":"json"})
     *
     * @param UserManager     $userManager
     * @param LoggerInterface $logger
     *
     * @return JsonResponse
     */
    public function renewTokenAction(UserManager $userManager): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $userManager->updateAccessToken($user);
        $userManager->updateUser($user);

        return new JsonResponse(['token' => $user->getToken()]);
    }
}