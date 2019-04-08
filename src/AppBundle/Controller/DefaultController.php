<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Event\UserEvent;
use AppBundle\Form\Type\LoginType;
use AppBundle\Form\Type\PasswordUpdateType;
use AppBundle\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Form\Type\RegistrationType;
use Doctrine\ORM\OptimisticLockException;
use AppBundle\Form\Type\PasswordResetType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class DefaultController extends Controller
{
    /**
     * @var UserManager;
     */
    private $userManager;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param UserManager              $userManager
     * @param EntityManagerInterface   $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        UserManager $userManager,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->userManager = $userManager;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/", name="app_index", methods={"GET"})
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('@App/Default/index.html.twig');
    }

    /**
     * @Route("/registration", name="app_registration", methods={"POST", "GET"})
     *
     * @param Request $request
     * @return Response
     *
     * @throws OptimisticLockException
     */
    public function registrationAction(Request $request): Response
    {
        $form = $this->createForm(RegistrationType::class);

        if ($request->isMethod(Request::METHOD_POST)) {
            $form->handleRequest($request);

            if (!$form->isSubmitted()) {
                return $this->render('@App/Default/registration.html.twig', [
                    'form' => $form->createView()
                ]);
            }

            if ($form->isValid()) {
                /** @var User $user */
                $user = $form->getData();

                $this->entityManager->persist($user);
                $this->userManager->updateUser($user, false);

                $this->eventDispatcher->dispatch(UserEvent::NEW_USER_REGISTERED, new UserEvent($user));

                return $this->render('@App/Default/registered.html.twig');
            }
        }

        return $this->render('@App/Default/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/registration/confirm/{token}", name="app_registration_confirm", methods={"GET"}, requirements={"token"="\w+"})
     *
     * @param $token
     * @return Response
     */
    public function confirmAction(string $token): Response
    {
        //todo: логику по подтверждению нужно будет перевезти в более подходящее место.
        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['confirmToken' => $token]);

        if (!$user) {
            return new Response('Пользователь с таким токеном подтверждения не найден.');
        }

        $user->setConfirmToken(null);
        $user->setEnabled(true);

        $this->entityManager->flush();

        return new Response('Регистрация подтверждена!');
    }

    /**
     * @Route("/login", name="app_login", methods={"GET"})
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function loginAction(AuthenticationUtils $authenticationUtils): Response
    {
        $form = $this->createForm(LoginType::class, null, [
            'action' => $this->generateUrl('app_login_check')
        ]);

        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('@App/Default/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error
        ]);
    }

    /**
     * @Route("/password/reset", name="app_password_reset", methods={"GET", "POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function resetPasswordAction(Request $request): Response
    {
        $form = $this->createForm(PasswordResetType::class);

        if ($request->isMethod(Request::METHOD_POST)) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();

                $user = $this->entityManager->getRepository(User::class)
                    ->findOneBy(['email' => $data['email']]);

                if (!$user) {
                    return new Response('Пользователь с указанным Email не найден', Response::HTTP_NOT_FOUND);
                }

                $this->userManager->updateResetToken($user);
                $this->entityManager->flush();

                $this->eventDispatcher->dispatch(UserEvent::RESET_TOKEN_UPDATED, new UserEvent($user));

                return new Response('На указанный вами Email была отправленна ссылка для смены пароля');
            }
        }

        return $this->render('@App/Default/password_reset.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/password/reset/{token}", name="app_password_reset_confirm", methods={"GET"})
     *
     * @param string $token
     * @return Response
     */
    public function resetPasswordConfirmAction(string $token): Response
    {
        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['resetToken' => $token]);

        if (!$user) {
            return new Response('Указан невалидный токен для сброса пароля', Response::HTTP_BAD_REQUEST);
        }

        $form = $this->createForm(PasswordResetType::class, ['resetToken' => $token], [
            'action' => $this->generateUrl('app_password_resetting'),
            'step' => PasswordResetType::STEP_SECOND
        ]);

        return $this->render('@App/Default/password_resetting.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/password/resetting", name="app_password_resetting", methods={"POST"})
     *
     * @param Request $request
     * @return Response
     *
     * @throws OptimisticLockException
     */
    public function resettingPasswordAction(Request $request): Response
    {
        $form = $this->createForm(PasswordResetType::class, null, [
            'step' => PasswordResetType::STEP_SECOND
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            $user = $this->entityManager->getRepository(User::class)
                ->findOneBy(['resetToken' => $data['resetToken']]);

            if (!$user) {
                return new Response('Указан невалидный токен для сброса пароля', Response::HTTP_BAD_REQUEST);
            }

            $user->setPlainPassword($data['password']);
            $user->setResetToken(null);

            $this->userManager->updateUser($user);

            return new Response('Пароль пользователя успешно изменен');
        }

        return $this->render('@App/Default/password_resetting.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @todo Необходимо выпилить позже из соображений безопастности.
     *
     * @Route("/confirm/links/{email}", name="app_show_confirm_links", methods={"GET"})
     *
     * @param string $email
     * @return Response
     */
    public function confirmLinksAction(string $email): Response
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if (!$user) {
            return new Response('Пользователь с указанным email не найден.');
        }

        $data = [];

        if ($user->getConfirmToken()) {
            $data['confirmToken'] = $user->getConfirmToken();
        }

        if ($user->getResetToken()) {
            $data['resetToken'] = $user->getResetToken();
        }


        return $this->render('@App/Default/confirm_links.html.twig', $data);
    }
}
