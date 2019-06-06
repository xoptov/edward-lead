<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Company;
use AppBundle\Service\UserManager;
use AppBundle\Entity\HistoryAction;
use AppBundle\Form\Type\CompanyType;
use AppBundle\Form\Type\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use AppBundle\Form\Type\PasswordUpdateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @param EntityManagerInterface   $entityManager
     * @param UserManager              $userManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserManager $userManager
    ) {
        $this->entityManager = $entityManager;
        $this->userManager = $userManager;
    }

    /**
     * @Route("/select-type", name="app_select_type", methods={"GET"})
     * @return Response
     */
    public function selectTypeAction(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->isTypeSelected()) {
            return $this->render('@App/User/select_type.html.twig');
        }

        if ($user->hasCompany()) {
            return $this->redirectToRoute('app_exchange');
        }

        return $this->redirectToRoute('app_exchange_my_leads');
    }

    /**
     * @Route("/creating/company", name="app_creating_company", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function creatingCompanyAction(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user->isTypeSelected()) {
            $this->addFlash('error', 'Тип пользователя уже указан');
            return $this->redirectToRoute('app_profile');
        }

        if ($user->getCompany()) {
            $this->addFlash('error', 'Компания для пользователя уже создана');
            return $this->redirectToRoute('app_profile');
        }

        $form = $this->createForm(CompanyType::class, null, ['mode' => CompanyType::MODE_COMPANY]);

        if ($request->isMethod(Request::METHOD_POST)) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                /** @var Company $company */
                $company = $form->getData();
                $company->setUser($user);
                $this->entityManager->persist($company);

                $user->switchToCompany()
                    ->makeTypeSelected();

                $this->entityManager->flush();

                return $this->redirectToRoute('app_profile');
            }
        }

        return $this->render('@App/User/company.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/stay/webmaster", name="app_stay_webmaster", methods={"GET"})
     *
     * @return Response
     */
    public function stayWebmasterAction(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user->isTypeSelected()) {
            return new Response('Тип пользователя уже указан', Response::HTTP_BAD_REQUEST);
        }

        $user->switchToWebmaster()->makeTypeSelected();

        $this->entityManager->flush();

        return $this->redirectToRoute('app_profile');
    }

    /**
     * @Route("/profile", name="app_profile", methods={"GET", "POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function profileAction(Request $request): Response
    {
        $profileForm = $this->createForm(ProfileType::class, $this->getUser());

        if ($request->isMethod(Request::METHOD_POST)) {
            $profileForm->handleRequest($request);

            if ($profileForm->isValid()) {
                $this->entityManager->flush();

                $this->addFlash('success', 'Данные профиля успешно обновлены');
            }
        }

        $passwordForm = $this->createForm(PasswordUpdateType::class, null, [
            'action' => $this->generateUrl('app_profile_update_password')
        ]);

        return $this->render('@App/User/profile.html.twig', [
            'profileForm' => $profileForm->createView(),
            'passwordForm' => $passwordForm->createView()
        ]);
    }

    /**
     * @Route("/history/login", name="app_history_login", methods={"GET"})
     *
     * @return Response
     */
    public function historyAction(): Response
    {
        $historyLogins = $this->entityManager->getRepository('AppBundle:HistoryAction')
            ->getByUserAndActionInDescOrder($this->getUser(), HistoryAction::ACTION_LOGIN);

        return $this->render('@App/User/history_logins.html.twig', [
            'historyLogins' => $historyLogins
        ]);
    }

    /**
     * @Route("/profile/password/update", name="app_profile_update_password", methods={"POST"})
     *
     * @param Request $request
     * @return Response
     *
     * @throws OptimisticLockException
     */
    public function updatePasswordAction(Request $request): Response
    {
        $passwordForm = $this->createForm(PasswordUpdateType::class);
        $passwordForm->handleRequest($request);

        /** @var User $user */
        $user = $this->getUser();

        if ($passwordForm->isValid()) {
            $data = $passwordForm->getData();
            $user->setPlainPassword($data['password']);
            $this->userManager->updateUser($user);

            $this->addFlash('success', 'Новый пароль успешно установлен');
        }

        $profileForm = $this->createForm(ProfileType::class, $user, [
            'action' => $this->generateUrl('app_profile')
        ]);

        return $this->render('@App/User/profile.html.twig', [
            'profileForm' => $profileForm->createView(),
            'passwordForm' => $passwordForm->createView()
        ]);
    }
}