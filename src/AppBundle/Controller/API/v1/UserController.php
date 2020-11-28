<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Entity\User;
use AppBundle\Util\Formatter;
use AppBundle\Event\UserEvent;
use AppBundle\Form\Type\UserType;
use AppBundle\Service\UserManager;
use AppBundle\Entity\User\Personal;
use AppBundle\Form\Type\PersonalType;
use AppBundle\Security\Voter\UserVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @Route("/api/v1")
 */
class UserController extends APIController
{
    /**
     * @Route(
     *     "/user/renew-token",
     *     name="api_v1_user_renew_token",
     *     methods={"GET"},
     *     defaults={"_format":"json"}
     * )
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
     * @Route(
     *  "/user/tutorial/has/{tmark}",
     *  name="api_v1_user_has_tutorial",
     *  methods={"GET"},
     *  defaults={"_format":"json"}
     * )
     * 
     * @param string $tmark
     * 
     * @return JsonResponse
     */
    public function hasTutorialAction($tmark): JsonResponse
    {
        $user = $this->getUser();

        $company = $this->isGranted(User::ROLE_COMPANY,$user);

        return new JsonResponse([ $tmark => $user->hasTutorialMark($tmark), 'company' => $company ]);
    }

    /**
     * @Route(
     *  "/user/tutorial/add/{tmark}",
     *  name="api_v1_user_add_tutorial",
     *  methods={"GET"},
     *  defaults={"_format":"json"}
     * )
     * 
     * @param UserManager   $userManager
     * @param string        $tmark
     * 
     * @return JsonResponse
     */
    public function addTutorialAction(
        UserManager $userManager, 
        $tmark
    ) {
        /** @var User $user */
        $user = $this->getUser();

        $mark_added = $user->addTutorialMark($tmark);

        if( $mark_added ){
            $userManager->updateUser($user);
        }
        
        return new JsonResponse([$tmark => $mark_added]);
    }

    /**
     * @Route("/user/me", name="api_v1_user_me_view", methods={"GET"})
     *
     * @param Request      $request
     * @param CacheManager $cacheManager
     *
     * @return JsonResponse
     */
    public function getMyAction(
        Request $request,
        CacheManager $cacheManager
    ): JsonResponse {

        /** @var User $user */
        $user = $this->getUser();

        return $this->getAction($user, $request, $cacheManager);
    }

    /**
     * @Route(
     *     "/user/{id}",
     *     name="api_v1_user_view",
     *     methods={"GET"},
     *     requirements={"id":"\d+"},
     *     defaults={"_format":"json"}
     * )
     *
     * @param User         $user
     * @param Request      $request
     * @param CacheManager $cacheManager
     *
     * @return JsonResponse
     */
    public function getAction(
        User $user,
        Request $request,
        CacheManager $cacheManager
    ): JsonResponse {

        if (!$this->isGranted(UserVoter::VIEW, $user)) {
            return new JsonResponse(
                ['У вас нет прав на просмотр информации о пользователе'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $result = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'phone' => Formatter::humanizePhone($user->getPhone()),
            'skype' => $user->getSkype(),
            'vkontakte' => $user->getVkontakte(),
            'facebook' => $user->getFacebook(),
            'telegram' => $user->getTelegram(),
            'type' => $user->getType()
        ];

        // todo: тут на самом делел херня получается, нужно представление картинки по другому.
        if ($user->hasLogotype()) {
            $result['logotype'] = [
                'id' => $user->getLogotype()->getId(),
                'path' => $cacheManager->getBrowserPath($user->getLogotype()->getPath(), 'logotype_202x202')
            ];
        } else {
            $result['logotype'] = null;
        }

        if ($user->hasPersonal()) {

            $personal = $user->getPersonal();

            $result['personal'] = [
                'fullName' => $personal->getFullName(),
            ];

            if ($personal->hasBirthDate()) {
                $birthDate = $personal->getBirthDate();
                $result['personal']['birthDate'] = $birthDate->getTimestamp() * 1000;
            } else {
                $result['personal']['birthDate'] = null;
            }

        } else {
            $result['personal'] = null;
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/user/{id}", name="api_v1_user_update", methods={"PATCH"})
     *
     * @param User                     $user
     * @param Request                  $request
     * @param EntityManagerInterface   $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return JsonResponse
     */
    public function patchAction(
        User $user,
        Request $request,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ): JsonResponse {

        if (!$this->isGranted(UserVoter::EDIT, $user)) {
            return new JsonResponse(['У вас не прав на редактирование пользователя'], Response::HTTP_FORBIDDEN);
        }

        $form = $this->createForm(UserType::class, $user, [
            'method' => Request::METHOD_PATCH,
            'csrf_protection' => false
        ]);

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return new JsonResponse(['Не отправлены данные'], Response::HTTP_BAD_REQUEST);
        }

        if (!$form->isValid()) {
            return $this->responseErrors($form->getErrors(true));
        }

        $entityManager->flush();

        $eventDispatcher->dispatch(UserEvent::UPDATED, new UserEvent($user));

        return new JsonResponse(['id' => $user->getId()]);
    }

    /**
     * @Route(
     *     "/user/{id}/settings",
     *     name="api_v1_user_settings",
     *     methods={"GET"},
     *     requirements={"id":"\d+"},
     *     defaults={"_format":"json"}
     * )
     *
     * @param User         $user
     * @param CacheManager $cacheManager
     *
     * @return JsonResponse
     */
    public function getMeSettings(
        User $user,
        CacheManager $cacheManager
    ): JsonResponse {

        if (!$this->isGranted(UserVoter::VIEW, $user)) {
            return new JsonResponse(
                ['У вас нет прав просмотра настроект пользователя'],
                Response::HTTP_FORBIDDEN
            );
        }

        $result = [
            'type' => $user->getType(),
            'logotype' => null
        ];

        /** @var Personal $personal */
        $personal = $user->getPersonal();

        $result['personal'] = [
            'fullName' => $personal->getFullName(),
            'birthDate' => null
        ];

        if ($personal->hasBirthDate()) {
            $result['personal']['birthDate'] = $personal->getBirthDate()->getTimestamp() * 1000;
        }

        if ($user->hasLogotype()) {

            $logotype = $user->getLogotype();

            $result['logotype'] = [
                'id' => $logotype->getId(),
                'path' => $cacheManager->getBrowserPath($logotype->getPath(), 'logotype_202x202')
            ];
        }

        return new JsonResponse($result);
    }

    /**
     * @Route(
     *     "/user/me/personal",
     *     name="api_v1_user_me_personal_view",
     *     methods={"GET"},
     *     defaults={"_format":"json"}
     * )
     *
     * @return JsonResponse
     */
    public function getMePersonal(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->getPersonalAction($user);
    }

    /**
     * @Route(
     *     "/user/{id}/personal",
     *     name="api_v1_user_personal_view",
     *     methods={"GET"},
     *     requirements={"id":"\d+"},
     *     defaults={"_format":"json"}
     * )
     *
     * @param User $user
     *
     * @return JsonResponse
     */
    public function getPersonalAction(User $user): JsonResponse
    {
        /** @var Personal $personal */
        $personal = $user->getPersonal();

        $result = [
            'fullName' => $personal->getFullName(),
            'birthDate' => null
        ];

        if ($personal->hasBirthDate()) {
            $birthDate = $personal->getBirthDate();
            $result['birthDate'] = $birthDate->getTimestamp() * 1000;
        }

        return new JsonResponse($result);
    }

    /**
     * @Route(
     *     "/user/me/personal",
     *     name="api_v1_user_me_personal_update",
     *     methods={"PUT"},
     *     defaults={"_format":"json"}
     * )
     *
     * @param Request                  $request
     * @param UserManager              $userManager
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return JsonResponse
     */
    public function putMePersonalAction(
        Request $request,
        UserManager $userManager,
        EventDispatcherInterface $eventDispatcher
    ): JsonResponse {

        /** @var User $user */
        $user =  $this->getUser();

        return $this->putPersonalAction($user, $request, $userManager, $eventDispatcher);
    }

    /**
     * @Route(
     *     "/user/{id}/personal",
     *     name="api_v1_user_personal_update",
     *     methods={"PUT"},
     *     requirements={"id":"\d+"},
     *     defaults={"_format":"json"}
     * )
     *
     * @param User                     $user
     * @param Request                  $request
     * @param UserManager              $userManager
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return JsonResponse
     */
    public function putPersonalAction(
        User $user,
        Request $request,
        UserManager $userManager,
        EventDispatcherInterface $eventDispatcher
    ): JsonResponse {

        if (!$this->isGranted(UserVoter::EDIT, $user)) {
            return new JsonResponse(
                ['У вас нет прав на редактирование персональной информации о пользователей'],
                Response::HTTP_FORBIDDEN
            );
        }

        $form = $this->createForm(PersonalType::class, null, [
            'method' => Request::METHOD_PUT,
            'csrf_protection' => false
        ]);

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return new JsonResponse(['Не отправлены данные'], Response::HTTP_BAD_REQUEST);
        }

        if (!$form->isValid()) {
            return $this->responseErrors($form->getErrors(true));
        }

        /** @var Personal $personal */
        $personal = $form->getData();
        $user->setPersonal($personal);

        $userManager->updateUser($user);

        $eventDispatcher->dispatch(UserEvent::PERSONAL_UPDATED, new UserEvent($user));

        return new JsonResponse(['id' => $user->getId()]);
    }
}