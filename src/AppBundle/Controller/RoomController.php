<?php

namespace AppBundle\Controller;

use Swift_Mailer;
use Swift_Message;
use AppBundle\Entity\Lead;
use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use AppBundle\Entity\Member;
use AppBundle\Event\RoomEvent;
use AppBundle\Form\Type\RoomType;
use AppBundle\Service\RoomManager;
use AppBundle\Service\AccountManager;
use AppBundle\Security\Voter\RoomVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RoomController extends Controller
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RoomManager
     */
    private $roomManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param EntityManagerInterface   $entityManager
     * @param RoomManager              $roomManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        RoomManager $roomManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->entityManager = $entityManager;
        $this->roomManager = $roomManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/rooms", name="app_rooms", methods={"GET"}, options={"_format": "json"})
     *
     * @return JsonResponse
     */
    public function getAllAction(): JsonResponse
    {
        $rooms = $this->entityManager->getRepository(Room::class)
            ->getByMember($this->getUser());

        $result = [];

        /** @var Room $room */
        foreach ($rooms as $room) {
            $result[] = [
                'id' => $room->getId(),
                'name' => $room->getName(),
                'leadCriteria' => $room->getLeadCriteria(),
                'leadPrice' => $room->getLeadPrice(),
                'platformWarranty' => $room->isPlatformWarranty()
            ];
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/room/create", name="app_room_create", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        $form = $this->createForm(RoomType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {

            /** @var Room $data */
            $data = $form->getData();
            $data
                ->setOwner($this->getUser())
                ->setEnabled(true);

            $this->entityManager->persist($data);

            try {
                $this->roomManager->joinInRoom($data, $this->getUser());
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());

                return $this->redirectToRoute('app_room_list');
            }

            $this->roomManager->updateInviteToken($data);
            $this->entityManager->flush();

            $this->addFlash('success', 'Новая комната успешно создана');

            return $this->redirectToRoute('app_room_list');
        }

        return $this->render('@App/v2/Room/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/room/list", name="app_room_list", methods={"GET"})
     *
     * @return Response
     */
    public function listAction(): Response
    {
        $user = $this->getUser();

        $rooms = $this->entityManager->getRepository(Room::class)
            ->getByMember($user);

        if (empty($rooms)) {
            return $this->render('@App/v2/Room/list.html.twig');
        }

        $rooms = array_map(function(Room $room) {
            return [
                'room' => $room,
                'daily' => 0,
                'reserved' => 0,
                'webmasters' => 0,
                'companies' => 0
            ];
        }, $rooms);

        $now = new \DateTime();

        $dailyLeads = $this->entityManager->getRepository(Lead::class)
            ->getAddedInRoomsByDate($rooms, $now);


        $reservedLeads = $this->entityManager->getRepository(Lead::class)
            ->getReservedInRooms($rooms);

        for ($i = 0; $i < count($rooms); $i++) {
            /** @var Lead $dailyLead */
            foreach ($dailyLeads as $dailyLead) {
                if ($dailyLead->getRoom() === $rooms[$i]['room']) {
                    $rooms[$i]['daily']++;
                }
            }

            /** @var Lead $reservedLead */
            foreach ($reservedLeads as $reservedLead) {
                if ($reservedLead->getRoom() === $rooms[$i]['room']) {
                    $rooms[$i]['reserved']++;
                }
            }

            $users = $this->entityManager->getRepository(User::class)
                ->getUsersInRoom($rooms[$i]['room']);

            /** @var User $user */
            foreach ($users as $user) {
                if ($user->isWebmaster()) {
                    $rooms[$i]['webmasters'] +=1 ;
                } elseif ($user->isCompany()) {
                    $rooms[$i]['companies'] += 1;
                }
            }
        }

        return $this->render('@App/v2/Room/list.html.twig', ['rooms' => $rooms]);
    }

    /**
     * @Route("/room/{id}", name="app_room_view", methods={"GET"})
     *
     * @param Room           $room
     * @param AccountManager $accountManager
     *
     * @return Response
     */
    public function viewAction(Room $room, AccountManager $accountManager): Response
    {
        $leads = $this->entityManager->getRepository(Lead::class)
            ->findBy(['room' => $room], ['createdAt' => 'DESC']);

        /** @var User $user */
        $user = $this->getUser();
        $availableBalance = $accountManager->getAvailableBalance($user->getAccount());
        $countCanBy = $availableBalance / $room->getLeadPrice();

        return $this->render('@App/v2/Room/view.html.twig', [
            'room' => $room,
            'leads' => $leads,
            'countCanBuy' => $countCanBy
        ]);
    }

    /**
     * @Route("/room/{room}/invite", name="app_room_invite", methods={"GET"})
     *
     * @param Room $room
     *
     * @return Response
     */
    public function inviteAction(Room $room): Response
    {
        return $this->render('@App/v2/Room/invite.html.twig', ['room' => $room]);
    }

    /**
     * @Route("/room/invite/invalid", name="app_room_invite_invalid", methods={"GET"})
     *
     * @return Response
     */
    public function inviteInvalidAction(): Response
    {
        return $this->render('@App/v2/Room/invite_invalid.html.twig');
    }

    /**
     * @Route("/room/invite/reject", name="app_room_invite_reject", methods={"GET"})
     *
     * @return Response
     */
    public function rejectInviteAction(): Response
    {
        return $this->render('@App/v2/Room/invite_reject.html.twig');
    }

    /**
     * @Route("/room/invite/{token}", name="app_room_invite_confirm", methods={"GET"})
     *
     * @param string $token
     *
     * @return Response
     */
    public function inviteConfirmAction(string $token): Response
    {
        $room = $this->entityManager->getRepository(Room::class)->findOneBy([
            'inviteToken' => $token,
            'enabled' => true
        ]);

        if (!$room) {
            return $this->redirectToRoute('app_room_invite_invalid');
        }

        return $this->render('@App/v2/Room/invite_confirm.html.twig', [
            'room' => $room
        ]);
    }

    /**
     * @Route("/room/invite/accept/{token}", name="app_room_invite_accept", methods={"GET"})
     *
     * @param string $token
     *
     * @return Response
     */
    public function acceptInviteAction(string $token): Response
    {
        $room = $this->entityManager->getRepository(Room::class)->findOneBy([
            'inviteToken' => $token,
            'enabled' => true
        ]);

        if (!$room) {
            return $this->redirectToRoute('app_room_invite_invalid');
        }

        try {
            $this->roomManager->joinInRoom($room, $this->getUser());
            $this->roomManager->updateInviteToken($room);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return $this->redirectToRoute('app_room_invite_invalid');
        }

        return $this->redirectToRoute('app_room_view', ['id' => $room->getId()]);
    }

    /**
     * @Route("/room/{room}/members", name="app_room_members", methods={"GET"}, defaults={"_format":"json"})
     *
     * @param Room         $room
     * @param CacheManager $cacheManager
     *
     * @return JsonResponse
     */
    public function getMembersAction(Room $room, CacheManager $cacheManager): JsonResponse
    {
        if (!$this->isGranted(RoomVoter::VIEW, $room)) {
            return new JsonResponse(['error' => 'Нет прав на просмотр списка членов группы'], Response::HTTP_FORBIDDEN);
        }

        $members = $this->entityManager->getRepository(Member::class)
            ->findBy(['room' => $room]);

        $result = [
            'companies' => [],
            'webmasters' => []
        ];

        foreach ($members as $member) {
            $user = $member->getUser();

            $item = [
                'id' => $member->getId(),
                'user' => [
                    'id' => $user->getId(),
                    'name' => $user->getName(),
                    'isOwner' => $room->isOwner($user),
                    'logotype' => null
                ]
            ];

            if ($user->isCompany() && $user->getCompany()->getLogotype()) {
                $logotype = $user->getCompany()->getLogotype();
                $item['user']['logotype'] = $cacheManager->getBrowserPath($logotype->getPath(), 'logotype_34x34');
            }

            if ($user->isWebmaster()) {
                $result['webmasters'][] = $item;
            } elseif ($user->isCompany()) {
                $result['companies'][] = $item;
            }
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/room/{room}/revoke/{member}", name="app_room_revoke_member", methods={"GET"}, defaults={"_format":"json"})
     *
     * @param Room   $room
     * @param Member $member
     *
     * @return JsonResponse
     */
    public function revokeMemberAction(Room $room, Member $member): JsonResponse
    {
        if (!$this->isGranted(RoomVoter::REVOKE_MEMBER, $room)) {
            return new JsonResponse(['error' => 'Нет прав на удаление пользователя'], Response::HTTP_FORBIDDEN);
        }

        $user = $member->getUser();

        $this->entityManager->remove($member);
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'Отозвано участие у пользователя в комнате',
            'room' => $room->getId(),
            'user' => $user->getId()
        ]);
    }

    /**
     * @Route("/room/{room}/deactivate", name="app_room_deactivate", methods={"GET"}, defaults={"_format":"json"})
     *
     * @param Room $room
     *
     * @return JsonResponse
     */
    public function deactivateAction(Room $room): JsonResponse
    {
        if (!$this->isGranted(RoomVoter::DEACTIVATE, $room)) {
            return new JsonResponse(['error' => 'Нет прав для деактивации комнаты'], Response::HTTP_FORBIDDEN);
        }

        $room->setEnabled(false);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(RoomEvent::DEACTIVATED, new RoomEvent($room));

        return new JsonResponse([
            'message' => 'Комната успешно деактивирована',
            'room' => $room->getId()
        ]);
    }

    /**
     * @Route("/room/send/invite", name="app_send_invite", methods={"POST"}, defaults={"_format": "json"})
     *
     * @param Request      $request
     * @param Swift_Mailer $mailer
     *
     * @return JsonResponse
     */
    public function sendInviteAction(Request $request, Swift_Mailer $mailer): JsonResponse
    {
        $formBuilder = $this->createFormBuilder(null, [
            'method' => Request::METHOD_POST,
            'csrf_protection' => false
        ]);

        $formBuilder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Необходимо указать email']),
                    new Email(['message' => 'Указал невалидный email']),
                ]
            ])
            ->add('token', HiddenType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Необходимо указать token'])
                ]
            ]);

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            $room = $this->entityManager->getRepository(Room::class)->findOneBy([
                'inviteToken' => $data['token']
            ]);

            if (!$room) {
                return new JsonResponse(['errors' => ['Невалидный токен приглашения']], Response::HTTP_BAD_REQUEST);
            }

            $content = $this->renderView('@App/v2/Room/invite_email.txt.twig', [
                'room' => $room,
                'inviteUrl' => $this->generateUrl('app_room_invite_confirm', ['token' => $data['token']], UrlGeneratorInterface::ABSOLUTE_URL)
            ]);

            $senderEmail = $this->getParameter('system_email');

            $message = new Swift_Message('Приглашение в комнату', $content);
            $message
                ->setFrom($senderEmail)
                ->setTo($data['email']);

            $mailer->send($message);

            return new JsonResponse(['message' => 'Приглашение в комнату принято в очередь на отправку']);
        }

        $errors = [];

        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }
}