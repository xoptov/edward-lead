<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Lead;
use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use AppBundle\Exception\RoomException;
use AppBundle\Form\Type\RoomType;
use AppBundle\Service\FeesManager;
use AppBundle\Service\RoomManager;
use AppBundle\Service\AccountManager;
use AppBundle\Security\Voter\RoomVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
     * @param EntityManagerInterface   $entityManager
     * @param RoomManager              $roomManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        RoomManager $roomManager
    ) {
        $this->entityManager = $entityManager;
        $this->roomManager = $roomManager;
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

            /** @var User $user */
            $user = $this->getUser();

            /** @var Room $data */
            $data = $form->getData();
            $data
                ->setOwner($user)
                ->setEnabled(true);

            $this->entityManager->persist($data);

            try {
                $this->roomManager->joinInRoom($data, $user);
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());

                return $this->redirectToRoute('app_room_list');
            }

            $this->roomManager->updateInviteToken($data);
            $this->entityManager->flush();

            $this->addFlash('success', 'Новая комната успешно создана');

            return $this->redirectToRoute('app_room_view', ['id' => $data->getId()]);
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
        /** @var User $user */
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
                'in_work' => 0,
                'webmasters' => 0,
                'companies' => 0
            ];
        }, $rooms);

        $now = new \DateTime();

        $repository = $this->entityManager->getRepository(Lead::class);

        $dailyLeads = $repository->getAddedInRoomsByDate($rooms, $now);
        $inWorkLeads = $repository->getOffersByRooms($rooms, [Lead::STATUS_IN_WORK]);

        for ($i = 0; $i < count($rooms); $i++) {
            /** @var Lead $dailyLead */
            foreach ($dailyLeads as $dailyLead) {
                if ($dailyLead->getRoom() === $rooms[$i]['room']) {
                    $rooms[$i]['daily']++;
                }
            }

            /** @var Lead $inWorkLead */
            foreach ($inWorkLeads as $inWorkLead) {
                if ($inWorkLead->getRoom() === $rooms[$i]['room']) {
                    $rooms[$i]['in_work']++;
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
     * @param FeesManager    $feesManager
     *
     * @return Response
     */
    public function viewAction(
        Room $room,
        AccountManager $accountManager,
        FeesManager $feesManager
    ): Response {
        if (!$this->isGranted(RoomVoter::VIEW, $room)) {
            $this->addFlash('error', 'У вас нет прав на просмотр комнаты');

            return $this->redirectToRoute('app_room_list');
        }

        $leads = $this->entityManager->getRepository(Lead::class)
            ->findBy(['room' => $room], ['createdAt' => 'DESC']);

        $buyers = $this->entityManager->getRepository(User::class)->getAdvertisersInRoom($room);

        $totalAvailableMoney = 0.0;

        foreach ($buyers as $buyer) {
            $totalAvailableMoney += $accountManager->getAvailableBalance($buyer->getAccount());
        }

        $buyerFee = $feesManager->getCommissionForBuyerInRoom($room);

        return $this->render('@App/v2/Room/view.html.twig', [
            'room' => $room,
            'leads' => $leads,
            'countCanBuy' => $this->roomManager->countCanBuy($room, $buyerFee, $totalAvailableMoney),
            'fee' => $feesManager->getCommissionForBuyerInRoom($room)
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

        /** @var User $user */
        $user = $this->getUser();

        try {
            $this->roomManager->joinInRoom($room, $user);
            $this->roomManager->updateInviteToken($room);
            $this->entityManager->flush();
        } catch (RoomException $e) {
            return $this->redirectToRoute('app_room_invite_invalid');
        }

        return $this->redirectToRoute('app_room_view', ['id' => $room->getId()]);
    }

    /**
     * @Route("/room/invite/reject/{token}", name="app_room_invite_reject", methods={"GET"})
     *
     * @param string $token
     *
     * @return Response
     */
    public function rejectInviteAction(string $token): Response
    {
        $room = $this->entityManager->getRepository(Room::class)->findOneBy([
            'inviteToken' => $token,
            'enabled' => true
        ]);

        if (!$room) {
            return $this->redirectToRoute('app_room_invite_invalid');
        }

        try {
            $this->roomManager->updateInviteToken($room);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return $this->redirectToRoute('app_room_invite_invalid');
        }

        return $this->render('@App/v2/Room/invite_reject.html.twig');
    }
}