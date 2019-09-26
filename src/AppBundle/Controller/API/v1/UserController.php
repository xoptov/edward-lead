<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Entity\User;
use AppBundle\Service\UserManager;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Response;
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
     * @param UserManager $userManager
     *
     * @return JsonResponse
     */
    public function renewTokenAction(UserManager $userManager): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $userManager->updateAccessToken($user);

        try {
            $userManager->updateUser($user);
        } catch (OptimisticLockException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['token' => $user->getToken()]);
    }
}