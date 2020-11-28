<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Lead;
use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use AppBundle\Entity\Member;
use AppBundle\Event\UserEvent;
use AppBundle\Service\UserManager;
use AppBundle\Entity\HistoryAction;
use AppBundle\Form\Type\ProfileType;
use AppBundle\Entity\UserDeleteRequest;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Repository\LeadRepository;
use AppBundle\Repository\RoomRepository;
use AppBundle\Entity\MonetaryTransaction;
use Doctrine\ORM\NonUniqueResultException;
use AppBundle\Form\Type\PasswordUpdateType;
use AppBundle\Repository\MemberRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Repository\HistoryActionRepository;
use AppBundle\Repository\MonetaryTransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/user")
 */
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
     * @Route("/select-role", name="app_user_select_role", methods={"GET"})
     *
     * @return Response
     */
    public function selectRoleAction(): Response
    {
        return $this->render('@App/User/select_role.html.twig');
    }

    /**
     * @Route("/information", name="app_user_information", methods={"GET", "PUT"})
     */
    public function editInformationAction(Request $request)
    {
        
    }

    /**
     * @Route("/stay/advertiser", name="app_user_stay_advertiser", methods={"GET"})
     *
     * @param TokenStorageInterface $tokenStorage
     * @param UserManager           $userManager
     *
     * @return Response
     */
    public function stayAdvertiserAction(
        TokenStorageInterface $tokenStorage,
        UserManager $userManager
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        if ($user->isRoleSelected()) {
            return new Response('Роль пользователя уже выбрана', Response::HTTP_BAD_REQUEST);
        }

        $user->switchToAdvertiser();
        $userManager->updateAccessToken($user);

        //todo: это костыль который пока не знаю как лучше изменить.
        $token = $tokenStorage->getToken();
        $newRoles = array_merge($token->getRoles(), [User::ROLE_ADVERTISER]);
        $tokenStorage->setToken(new UsernamePasswordToken($user, $token->getCredentials(), 'main', $newRoles));

        $this->entityManager->flush();

        return $this->redirectToRoute('app_user_advertiser_profile');
    }

    /**
     * @Route("/stay/webmaster", name="app_user_stay_webmaster", methods={"GET"})
     *
     * @param TokenStorageInterface $tokenStorage
     *
     * @return Response
     */
    public function stayWebmasterAction(
        TokenStorageInterface $tokenStorage
    ): Response {

        /** @var User $user */
        $user = $this->getUser();

        if ($user->isRoleSelected()) {
            return new Response('Роль пользователя уже выбрана', Response::HTTP_BAD_REQUEST);
        }

        $user->switchToWebmaster();
        $this->userManager->updateAccessToken($user);

        //todo: это костыль который пока не знаю как лучше изменить.
        $token = $tokenStorage->getToken();
        $newRoles = array_merge($token->getRoles(), [User::ROLE_WEBMASTER]);
        $tokenStorage->setToken(new UsernamePasswordToken($user, $token->getCredentials(), 'main', $newRoles));

        $this->entityManager->flush();

        return $this->redirectToRoute('app_user_profile');
    }

    /**
     * @Route("/advertiser/profile", name="app_user_advertiser_profile", methods={"GET"})
     *
     * @return Response
     */
    public function advertiserProfileAction(): Response
    {
        return $this->render('@App/v3/User/advertiser_profile.html.twig');
    }

    /**
     * @Route("/profile", name="app_user_profile", methods={"GET", "POST"})
     *
     * @param Request $request
     *
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
            'action' => $this->generateUrl('app_user_update_password')
        ]);

        return $this->render('@App/User/profile.html.twig', [
            'profileForm' => $profileForm->createView(),
            'passwordForm' => $passwordForm->createView()
        ]);
    }

    /**
     * @Route("/history/login", name="app_user_history_login", methods={"GET"})
     *
     * @return Response
     */
    public function historyAction(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var HistoryActionRepository $repository */
        $repository = $this->entityManager
            ->getRepository('AppBundle:HistoryAction');

        $historyLogins = $repository->getByUserAndActionInDescOrder(
            $user, 
            HistoryAction::ACTION_LOGIN
        );

        return $this->render('@App/User/history_logins.html.twig', [
            'historyLogins' => $historyLogins
        ]);
    }

    /**
     * @Route("/password/update", name="app_user_update_password", methods={"POST"})
     *
     * @param Request                  $request
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return Response
     */
    public function updatePasswordAction(
        Request $request,
        EventDispatcherInterface $eventDispatcher
    ): Response {

        $passwordForm = $this->createForm(PasswordUpdateType::class);
        $passwordForm->handleRequest($request);

        /** @var User $user */
        $user = $this->getUser();

        if ($passwordForm->isValid()) {
            $data = $passwordForm->getData();
            $user->setPlainPassword($data['password']);
            $this->userManager->updateUser($user);

            $eventDispatcher->dispatch(
                UserEvent::PASSWORD_CHANGED, 
                new UserEvent($user)
            );

            $this->addFlash('success', 'Новый пароль успешно установлен');
        }

        $profileForm = $this->createForm(ProfileType::class, $user, [
            'action' => $this->generateUrl('app_user_profile')
        ]);

        return $this->render('@App/User/profile.html.twig', [
            'profileForm' => $profileForm->createView(),
            'passwordForm' => $passwordForm->createView()
        ]);
    }

    /**
     * @Route("/request/delete", name="app_user_request_delete", methods={"GET"})
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

        /** @var User $user */
        $user = $this->getUser();

        $deleteRequest = new UserDeleteRequest();
        $deleteRequest
            ->setUser($user);

        $this->entityManager->persist($deleteRequest);
        $this->entityManager->flush();

        $this->addFlash('success', 'Запрос на удаление аккаунта принят');

        //todo: тут нужно добавить создание обращения в техподдержку.

        return $this->redirectToRoute('app_user_profile');
    }

    /**
     * @Route("/dashboard", name="app_user_dashboard", methods={"GET"})
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
            'addedLeadsLastMonth' => [],
            'averageTarget' => 0
        ];

        /** @var LeadRepository $leadRepository */
        $leadRepository = $this->entityManager->getRepository(Lead::class);

        $now = new \DateTime();

        $result['addedLeadsToday'] = $leadRepository->getAddedCountByDate($now);

        $totalTarget = $leadRepository->getCountByStatus(Lead::STATUS_TARGET);
        $totalNotTarget = $leadRepository->getCountByStatus(Lead::STATUS_NOT_TARGET);

        if ($totalTarget && $totalNotTarget) {
            $result['averageTarget'] = $totalTarget / (($totalTarget + $totalNotTarget) / 100);
        } elseif ($totalTarget) {
            $result['averageTarget'] = 100;
        }

        /** @var User $user */
        $user = $this->getUser();

        /** @var RoomRepository $roomRepository */
        $roomRepository = $this->entityManager->getRepository(Room::class);
        $rooms = $roomRepository->getByMember($user);

        /** @var MemberRepository */
        $memberRepository = $this->entityManager->getRepository(Member::class);
        $members = $memberRepository->getByRooms($rooms);

        $dailyLeads = $leadRepository->getAddedInRoomsByDate($rooms, $now);
        $doneLeads = $leadRepository->getOffersByRooms($rooms, [Lead::STATUS_TARGET, Lead::STATUS_NOT_TARGET]);
        
        $result['addedLeadsLastMonth'] = $leadRepository->getCountByUserLastMonth($user);
        $result['addedLeadsLastDay'] = $leadRepository->getCountByUserLastDay($user);

        /** @var Room $room */
        foreach ($rooms as $room) {
            $row = [
                'room' => $room,
                'daily' => 0,
                'target' => 0,
                'notTarget' => 0,
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
                    if ($doneLead->getStatus() === Lead::STATUS_TARGET) {
                        $row['target']++;
                    }
                    if ($doneLead->getStatus() === Lead::STATUS_NOT_TARGET) {
                        $row['notTarget']++;
                    }
                }
            }

            if ($row['notTarget'] && $row['target']) {
                $row['averageTarget'] = $row['target'] / (($row['target'] + $row['notTarget']) / 100);
            } elseif ($row['target']) {
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
                } elseif ($memberUser->isAdvertiser()) {
                    $row['companies']++;
                }
            }

            $result['list'][] = $row;
        }

        /** @var User $user */
        $user = $this->getUser();

        /** @var MonetaryTransactionRepository $transactionRepository */
        $transactionRepository = $this->entityManager->getRepository(MonetaryTransaction::class);

        $lastIncomes = $transactionRepository->getIncoming($user->getAccount());
        $result['lastIncomes'] = $lastIncomes;

        $result['lastLeads'] = $leadRepository->findBy([
            'user' => $user
        ], ['createdAt' => 'DESC'], 6);

        return $this->render('@App/v3/User/dashboard.html.twig', ['data' => $result]);
    }
}
