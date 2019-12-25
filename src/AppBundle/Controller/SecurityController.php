<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Psr\Log\LoggerInterface;
use AppBundle\Event\UserEvent;
use Doctrine\DBAL\DBALException;
use AppBundle\Form\Type\LoginType;
use AppBundle\Service\UserManager;
use AppBundle\Entity\ClientAccount;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Form\Type\RegistrationType;
use AppBundle\Form\Type\PasswordResetType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SecurityController extends Controller
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @param EntityManagerInterface   $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param UserManager              $userManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        UserManager $userManager
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->userManager = $userManager;
    }

    /**
     * @Route("/registration", name="app_registration", methods={"POST", "GET"})
     *
     * @param Request                        $request
     * @param LoggerInterface                $logger
     * @param AuthenticationManagerInterface $authenticationManager
     * @param TokenStorageInterface          $tokenStorage
     *
     * @return Response
     */
    public function registrationAction(
        Request $request,
        LoggerInterface $logger,
        AuthenticationManagerInterface $authenticationManager,
        TokenStorageInterface $tokenStorage
    ): Response {

        $form = $this->createForm(RegistrationType::class);

        if ($request->isMethod(Request::METHOD_POST)) {
            $form->handleRequest($request);

            if (!$form->isSubmitted()) {
                return $this->render('@App/Security/registration.html.twig', [
                    'form' => $form->createView()
                ]);
            }

            if ($form->isValid()) {

                /** @var User $user */
                $user = $form->getData();

                $plainPassword = $user->getPlainPassword();

                $user->setEnabled(true);

                $this->entityManager->persist($user);
                $this->userManager->updateUser($user, false);

                //todo надо подумать куда вынести этот код в лучшее место.
                if ($request->cookies->has('referrer')) {

                    $referrerToken = $request->cookies->get('referrer');

                    if (!empty($referrerToken)) {

                        $userRepository = $this->entityManager->getRepository(User::class);

                        try {
                            $referrerUser = $userRepository
                                ->getByReferrerToken($referrerToken);
                        } catch (DBALException $e) {
                            $logger->error('Ошибка привязки пользователья к реферреру', [
                                'message' => $e->getMessage()
                            ]);
                        }

                        if (isset($referrerUser) && $referrerUser) {
                            $user->setReferrer($referrerUser);
                        }
                    }
                }

                //todo конец кода который нужно по хорошему вынести отдельно.

                $account = new ClientAccount();
                $account
                    ->setUser($user)
                    ->setEnabled(true);

                $this->entityManager->persist($account);
                $this->entityManager->flush();

                //todo возможно нужно в листнер вынести на событие NEW_REGISTERED
                $token = new UsernamePasswordToken($user, $plainPassword, 'main');
                $resultToken = $authenticationManager->authenticate($token);
                $tokenStorage->setToken($resultToken);

                $this->eventDispatcher->dispatch(UserEvent::NEW_REGISTERED, new UserEvent($user));

                return $this->redirectToRoute('app_registered');
            }
        }

        return $this->render('@App/Security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/registered", name="app_registered", methods={"GET"})
     *
     * @return Response
     */
    public function registeredAction(): Response
    {
        return $this->render('@App/Security/registered.html.twig');
    }

    /**
     * @Route("/registration/confirm/{token}", name="app_registration_confirm", methods={"GET"}, requirements={"token"="\w+"})
     *
     * @param $token
     *
     * @return Response
     */
    public function confirmAction(string $token): Response
    {
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
     *
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     */
    public function loginAction(AuthenticationUtils $authenticationUtils): Response
    {
        $form = $this->createForm(LoginType::class, null, [
            'action' => $this->generateUrl('app_login_check')
        ]);

        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('@App/v3/Security/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error
        ]);
    }

    /**
     * @Route("/password/reset", name="app_password_reset", methods={"GET", "POST"})
     *
     * @param Request $request
     *
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

                if (!$user){
                    return $this->render('@App/v3/Security/password_reset.html.twig', [
                        'form' => $form->createView(),
                        'error' => 'Пользователь с указанным Email не найден'
                    ]);
                }

                $this->userManager->updateResetToken($user);
                $this->entityManager->flush();

                $this->eventDispatcher->dispatch(UserEvent::RESET_TOKEN_UPDATED, new UserEvent($user));

                return new Response('На указанный вами Email была отправленна ссылка для смены пароля');
            }
        }

        return $this->render('@App/v3/Security/password_reset.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/password/reset/{token}", name="app_password_reset_confirm", methods={"GET"})
     *
     * @param string $token
     *
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

        return $this->render('@App/Security/password_resetting.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/password/resetting", name="app_password_resetting", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
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

            $this->eventDispatcher->dispatch(
                UserEvent::PASSWORD_CHANGED,
                new UserEvent($user)
            );

            // return new Response('Пароль пользователя успешно изменен');
            return $this->render('@App/v3/Security/password_change_success.html.twig');
        }

        return $this->render('@App/Security/password_resetting.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/profile/sessions/clear", name="app_sessions_clear", methods={"GET"})
     *
     * @param SessionStorageInterface $sessionStorage
     * @return Response
     */
    public function endActiveSessionsAction(SessionStorageInterface $sessionStorage)
    {
        $sessionStorage->clear();

        return new Response('All active session is cleared!');
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


        return $this->render('@App/Security/confirm_links.html.twig', $data);
    }
}