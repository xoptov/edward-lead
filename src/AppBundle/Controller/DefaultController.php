<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Event\UserEvent;
use AppBundle\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Form\Type\RegistrationType;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
     * @Route("/registration", name="app_registration", methods={"POST", "GET"})
     *
     * @param Request
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

                //todo: убрать генерацию и назначение токена в более подходящее место.
                $confirmToken = $this->userManager->generateConfirmToken();
                $user->setConfirmToken($confirmToken);

                $this->userManager->updateUser($user);

                // todo: а на листнере мы будем перехватывать и посылать письмо на email.
                $this->eventDispatcher->dispatch(UserEvent::NEW_USER_REGISTERED, new UserEvent($user));

                return new Response('Регистрационные данные приняты! На указанный Вами ящих будет отправлена ссылка с подтверждением регистрации.');
            }
        }

        return $this->render('@App/Default/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/registration/confirm/{token}", name="app_registration_confirm", methods={"GET"}, requirements={"token"="\w+"})
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
}
