<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Lead;
use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use AppBundle\Entity\Image;
use AppBundle\Entity\Member;
use AppBundle\Entity\Company;
use AppBundle\Service\UserManager;
use AppBundle\Entity\HistoryAction;
use AppBundle\Form\Type\CompanyType;
use AppBundle\Form\Type\ProfileType;
use AppBundle\Entity\UserDeleteRequest;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\MonetaryTransaction;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\NonUniqueResultException;
use AppBundle\Security\Voter\CompanyVoter;
use AppBundle\Form\Type\PasswordUpdateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
     *
     * @return Response
     */
    public function selectTypeAction(): Response
    {
        return $this->render('@App/User/select_type.html.twig');
    }

    /**
     * @Route("/company/create", name="app_creating_company", methods={"GET", "POST"})
     *
     * @param Request               $request
     * @param TokenStorageInterface $tokenStorage
     *
     * @return Response
     */
    public function creatingCompanyAction(Request $request, TokenStorageInterface $tokenStorage): Response
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

        $form = $this->createForm(CompanyType::class, null, [
            'mode' => CompanyType::MODE_COMPANY,
            'validation_groups' => ['Default', 'Company']
        ]);

        if ($request->isMethod(Request::METHOD_POST)) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                /** @var Company $company */
                $company = $form->getData();
                $company->setUser($user);
                $this->entityManager->persist($company);

                $user->switchToCompany()
                    ->makeTypeSelected();

                //todo: это костыль но пока незнаю как лучше сделать.
                $token = $tokenStorage->getToken();
                $newRoles = array_merge($user->getRoles(), ['ROLE_COMPANY']);
                $tokenStorage->setToken(new UsernamePasswordToken($user, $token->getCredentials(), 'main', $newRoles));

                $logotypePath = $company->getLogotypePath();

                if ($logotypePath) {
                    $pathParts = pathinfo($logotypePath);
                    $image = $this->entityManager->getRepository(Image::class)
                        ->findOneBy(['filename' => $pathParts['basename']]);

                    if ($image) {
                        $company->setLogotype($image);
                    } else {
                        $image = new Image();
                        $image
                            ->setFilename($pathParts['basename'])
                            ->setPath($logotypePath);

                        $this->entityManager->persist($image);
                        $company->setLogotype($image);
                    }
                }

                $this->entityManager->flush();

                $this->addFlash('success', 'Компания создана');

                return $this->redirectToRoute('app_updating_office', [
                    'id' => $company->getId()
                ]);
            }
        }

        return $this->render('@App/User/company.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/company/update/{id}", name="app_updating_company", methods={"GET", "PUT"})
     *
     * @param Request $request
     * @param Company $company
     *
     * @return Response
     */
    public function updateCompanyAction(Request $request, Company $company): Response
    {
        if (!$this->isGranted(CompanyVoter::EDIT, $company)) {
            return new Response('Редактирование чужой компании запрещено');
        }

        $form = $this->createForm(CompanyType::class, $company, [
            'method' => Request::METHOD_PUT,
            'mode' => CompanyType::MODE_COMPANY,
            'validation_groups' => ['Default', 'Company']
        ]);

        if ($request->isMethod(Request::METHOD_PUT)) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $logotypePath = $company->getLogotypePath();

                if ($logotypePath) {
                    $pathParts = pathinfo($logotypePath);

                    $image = $this->entityManager->getRepository(Image::class)
                        ->findOneBy(['filename' => $pathParts['basename']]);

                    if ($image) {
                        $company->setLogotype($image);
                    } else {
                        $image = new Image();
                        $image
                            ->setPath($logotypePath)
                            ->setFilename($pathParts['basename']);

                        $this->entityManager->persist($image);
                        $company->setLogotype($image);
                    }
                }

                $this->entityManager->flush();

                $this->addFlash('success', 'Информация о компании обновлена');

                return $this->redirectToRoute('app_updating_office', ['id' => $company->getId()]);
            }
        }

        return $this->render('@App/User/company.html.twig', [
            'form' => $form->createView(),
            'company' => $company
        ]);
    }

    /**
     * @Route("/office/update/{id}", name="app_updating_office", methods={"GET", "PUT"})
     *
     * @param Request $request
     * @param Company $company
     *
     * @return Response
     */
    public function updateOfficeAction(Request $request, Company $company): Response
    {
        if (!$this->isGranted(CompanyVoter::EDIT, $company)) {
            return new Response('Редактирование чужого офиса запрещено');
        }

        $form = $this->createForm(CompanyType::class, $company, [
            'method' => Request::METHOD_PUT,
            'mode' => CompanyType::MODE_OFFICE,
            'validation_groups' => ['Default', 'Office']
        ]);

        if ($request->isMethod(Request::METHOD_PUT)) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->entityManager->flush();

                $this->addFlash('success', 'Информация о офисе сохранена');

                return $this->redirectToRoute('app_profile');
            }
        }

        return $this->render('@App/User/office.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/stay/webmaster", name="app_stay_webmaster", methods={"GET"})
     *
     * @param TokenStorageInterface $tokenStorage
     * @param UserManager           $userManager
     *
     * @return Response
     */
    public function stayWebmasterAction(TokenStorageInterface $tokenStorage, UserManager $userManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user->isTypeSelected()) {
            return new Response('Тип пользователя уже указан', Response::HTTP_BAD_REQUEST);
        }

        $user
            ->switchToWebmaster()
            ->makeTypeSelected();

        $userManager->updateAccessToken($user);

        //todo: это костыль который пока не знаю как лучше изменить.
        $token = $tokenStorage->getToken();
        $newRoles = array_merge($token->getRoles(), ['ROLE_WEBMASTER']);
        $tokenStorage->setToken(new UsernamePasswordToken($user, $token->getCredentials(), 'main', $newRoles));

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

    /**
     * @Route("/request/delete", name="app_request_delete", methods={"GET"})
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function deleteRequestAction(): Response
    {
        $deleteRequest = $this->entityManager
            ->getRepository(UserDeleteRequest::class)
            ->findOneBy(['user' => $this->getUser()]);

        if ($deleteRequest) {
            throw new \Exception('Запрос на удаление пользователя уже отправлен');
        }

        $deleteRequest = new UserDeleteRequest();
        $deleteRequest
            ->setUser($this->getUser());

        $this->entityManager->persist($deleteRequest);
        $this->entityManager->flush();

        $this->addFlash('success', 'Запрос на удаление аккаунта принят');

        //todo: тут нужно добавить создание обращения в техподдержку.

        return $this->redirectToRoute('app_profile');
    }

    /**
     * @Route("/dashboard", name="app_dashboard", methods={"GET"})
     *
     * @return Response
     *
     * @throws NonUniqueResultException
     */
    public function dashboardAction(): Response
    {
        $result = [
            'list' => [],
            'addedLeadsToday' => 0,
            'averageTarget' => 0
        ];

        $leadRepository = $this->entityManager->getRepository(Lead::class);

        $now = new \DateTime();

        $result['addedLeadsToday'] = $leadRepository->getCountAddedByDate($now);

        $totalSoldCount = $leadRepository->getCountByStatus(Lead::STATUS_SOLD);
        $totalNoTargetCount = $leadRepository->getCountByStatus(Lead::STATUS_NO_TARGET);

        if ($totalSoldCount && $totalNoTargetCount) {
            $result['averageTarget'] = $totalSoldCount / (($totalSoldCount + $totalNoTargetCount) / 100);
        } elseif ($totalSoldCount) {
            $result['averageTarget'] = 100;
        }

        $rooms = $this->entityManager->getRepository(Room::class)
            ->getByMember($this->getUser());

        $members = $this->entityManager->getRepository(Member::class)
            ->getByRooms($rooms);

        $dailyLeads = $leadRepository->getAddedInRoomsByDate($rooms, $now);
        $doneLeads = $leadRepository->getByRoomsAndDone($rooms);

        /** @var Room $room */
        foreach ($rooms as $room) {
            $row = [
                'room' => $room,
                'daily' => 0,
                'sold' => 0,
                'noTarget' => 0,
                'averageTarget' => 0,
                'webmasters' => 0,
                'companies' => 0
            ];

            /** @var Lead $dailyLead */
            foreach ($dailyLeads as $dailyLead) {
                if ($dailyLead->getRoom() === $room) {
                    $row['daily']++;
                }
            }

            /** @var Lead $doneLead */
            foreach ($doneLeads as $doneLead) {
                if ($doneLead->getRoom() === $room) {
                    if ($doneLead->isSold()) {
                        $row['sold']++;
                    }
                    if ($doneLead->isNoTarget()) {
                        $row['noTarget']++;
                    }
                }
            }

            if ($row['noTarget'] && $row['sold']) {
                $row['averageTarget'] = $row['sold'] / (($row['sold'] + $row['noTarget']) / 100);
            } elseif ($row['sold']) {
                $row['averageTarget'] = 100;
            }

            /** @var Member $member */
            foreach ($members as $member) {
                if ($member->getRoom() !== $room) {
                    continue;
                }
                $memberUser = $member->getUser();
                if ($memberUser->isWebmaster()) {
                    $row['webmasters']++;
                } elseif ($memberUser->isCompany()) {
                    $row['companies']++;
                }
            }

            $result['list'][] = $row;
        }

        /** @var User $user */
        $user = $this->getUser();

        $lastIncomes = $this->entityManager->getRepository(MonetaryTransaction::class)
            ->getIncoming($user->getAccount());
        $result['lastIncomes'] = $lastIncomes;

        $result['lastLeads'] = $leadRepository->findBy(['status' => Lead::STATUS_ACTIVE], ['createdAt' => 'DESC'], 5);

        return $this->render('@App/User/dashboard.html.twig', ['data' => $result]);
    }
}
