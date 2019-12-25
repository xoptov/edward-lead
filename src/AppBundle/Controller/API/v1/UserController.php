<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Entity\User;
use AppBundle\Util\Formatter;
use AppBundle\Event\UserEvent;
use AppBundle\Service\UserManager;
use AppBundle\Entity\User\Personal;
use AppBundle\Form\Type\PersonalType;
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
     * @Route("/user/my", name="api_v1_user_my_view", methods={"GET"})
     *
     * @param Request       $request
     * @param CacheManager  $cacheManager
     *
     * @return JsonResponse
     */
    public function getMyAction(
        Request $request,
        CacheManager $cacheManager
    ): JsonResponse {

        /** @var User $user */
        $user = $this->getUser();

        $result = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'phone' => Formatter::humanizePhone($user->getPhone()),
            'skype' => $user->getSkype(),
            'vkontakte' => $user->getVkontakte(),
            'facebook' => $user->getFacebook(),
            'telegram' => $user->getTelegram()
        ];

        // todo: тут на самом делел херня получается, нужно представление картинки по другому.
        if ($user->hasLogotype()) {
            $result['logotype'] = $cacheManager->getBrowserPath($user->getLogotype()->getPath(), 'logotype_202x202');
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

        if ($user->hasCompany()) {
            $company = $user->getCompany();
            if ($request->get('deep')) {
                $result['company'] = [
                    'id' => $company->getId(),
                    'shortName' => $company->getShortName(),
                    'largeName' => $company->getLargeName(),
                    'inn' => $company->getInn(),
                    'ogrn' => $company->getOgrn(),
                    'kpp' => $company->getKpp(),
                    'bik' => $company->getBik(),
                    'accountNumber' => $company->getAccountNumber(),
                    'address' => $company->getAddress(),
                    'zipcode' => $company->getZipcode()
                ];
            } else {
                $result['company'] = ['id' => $company->getId()];
            }
        } else {
            $result['company'] = null;
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/user/my/personal", name="api_v1_user_my_personal_view", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getMyPersonalAction(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $result = [];

        if ($user->hasPersonal()) {

            /** @var Personal $personal */
            $personal = $user->getPersonal();
            $result['fullName'] = $personal->getFullName();

            if ($personal->hasBirthDate()) {
                $birthDate = $personal->getBirthDate();
                $result['birthDate'] = $birthDate->getTimestamp() * 1000;
            }
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/user/my/personal", name="api_v1_user_my_personal_create", methods={"POST"})
     *
     * @param Request                  $request
     * @param UserManager              $userManager
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return JsonResponse
     */
    public function postMyPersonalAction(
        Request $request,
        UserManager $userManager,
        EventDispatcherInterface $eventDispatcher
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(PersonalType::class, null, [
            'csrf_protection' => false
        ]);

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return new JsonResponse(['Не отправлены данные'], Response::HTTP_BAD_REQUEST);
        }

        if (!$form->isValid()) {
            return $this->responseErrors($form->getErrors(true));
        }

        $personal = $form->getData();
        $user->setPersonal($personal);

        $userManager->updateUser($user);

        $eventDispatcher->dispatch(UserEvent::PERSONAL_CREATED, new UserEvent($user));

        return new JsonResponse(['id' => $user->getId()]);
    }

    /**
     * @Route("/user/my/personal", name="api_v1_user_my_personal_update", methods={"PUT"})
     *
     * @param Request                  $request
     * @param UserManager              $userManager
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return JsonResponse
     */
    public function putMyPersonalAction(
        Request $request,
        UserManager $userManager,
        EventDispatcherInterface $eventDispatcher
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();

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

        $personal = $form->getData();
        $user->setPersonal($personal);

        $userManager->updateUser($user);

        $eventDispatcher->dispatch(UserEvent::PERSONAL_UPDATED, new UserEvent($user));

        return new JsonResponse(['id' => $user->getId()]);
    }
}
