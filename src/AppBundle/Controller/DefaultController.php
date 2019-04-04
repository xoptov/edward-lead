<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Event\UserEvent;
use AppBundle\Form\Type\LoginType;
use AppBundle\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Form\Type\RegistrationType;
use Doctrine\ORM\OptimisticLockException;
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
     * @Route("/reset", name="app_reset", methods={"GET"})
     * @return Response
     */
    public function resetAction(): Response
    {
        return $this->render('@App/Default/reset.html.twig');
    }
}
