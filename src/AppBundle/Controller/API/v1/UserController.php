<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Entity\User;
use AppBundle\Event\UserEvent;
use AppBundle\Service\UserManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @Route("/api/v1")
 */
class UserController extends Controller
{
    /**
     * @Route("/user/renew-token", name="api_v1_user_renew_token", methods={"GET"}, defaults={"_format":"json"})
     *
     * @param UserManager              $userManager
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return JsonResponse
     */
    public function renewTokenAction(
        UserManager $userManager,
        EventDispatcherInterface $eventDispatcher
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();

        $userManager->updateAccessToken($user);
        $userManager->updateUser($user);

        $eventDispatcher->dispatch(
            UserEvent::API_TOKEN_CHANGED,
            new UserEvent($user)
        );

        return new JsonResponse(['token' => $user->getToken()]);
    }

    /**
     * @Route("/user/tutorial/has/{tmark}",name="api_v1_user_has_tutorial", methods={"GET"}, defaults={"_format":"json"})
     * 
     * @param string $tmark
     * 
     * @return JsonResponse
     */
    public function hasTutorialAction($tmark)
    {
        $user = $this->getUser();

        $company = $this->isGranted(User::ROLE_COMPANY,$user);

        return new JsonResponse([ $tmark => $user->hasTutorialMark($tmark), 'company' => $company ]);
    }

    /**
     * @Route("/user/tutorial/add/{tmark}",name="api_v1_user_add_tutorial", methods={"GET"}, defaults={"_format":"json"})
     * 
     * @param UserManager   $userManager
     * @param string        $tmark
     * 
     * @return JsonResponse
     */
    public function addTutorialAction(
        UserManager $userManager, 
        $tmark)
    {
        $user = $this->getUser();

        $mark_added = $user->addTutorialMark($tmark);

        if( $mark_added ){
            $userManager->updateUser($user);
        }
        
        return new JsonResponse([$tmark => $mark_added]);
    }
}