<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Room;
use AppBundle\Form\Type\RoomType;
use AppBundle\Service\RoomManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @param EntityManagerInterface $entityManager
     * @param RoomManager            $roomManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        RoomManager $roomManager
    ) {
        $this->entityManager = $entityManager;
        $this->roomManager = $roomManager;
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
     * @Route("/room/create", name="app_room", methods={"GET", "POST"})
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

                return $this->redirectToRoute('app_profile');
            }

            $this->roomManager->updateInviteToken($data);
            $this->entityManager->flush();

            $this->addFlash('success', 'Новая комната успешно создана');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('@App/Room/create.html.twig', ['form' => $form->createView()]);
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


        return $this->render('@App/Room/list.html.twig', ['rooms' => $rooms]);
    }

    /**
     * @Route("/room/{room}/invite-token", name="app_room_invite_token", methods={"GET"})
     *
     * @param Room $room
     *
     * @return Response
     */
    public function inviteToken(Room $room): Response
    {
        return $this->render('@App/Room/invite_token.html.twig');
    }

    /**
     * @Route("/room/invite/{token}", name="app_room_invite", methods={"GET"})
     *
     * @param string $token
     *
     * @return Response
     */
    public function inviteAction(string $token): Response
    {
        $room = $this->entityManager->getRepository(Room::class)->findOneBy([
            'inviteToken' => $token,
            'enabled' => true
        ]);

        if (!$room) {
            $this->addFlash('error', 'Невалидное приглашение в комнату');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('@App/Room/invite.html.twig', [
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
            $this->addFlash('error', 'Невалидное приглашение в группу');

            return $this->redirectToRoute('app_profile');
        }

        try {
            $this->roomManager->joinInRoom($room, $this->getUser());
            $this->roomManager->updateInviteToken($room);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return $this->redirectToRoute('app_profile');
        }

        return new Response('Вы вступили в приглашённую группу');
    }

    /**
     * @Route("/room/invite/reject", name="app_room_invite_reject", methods={"GET"})
     *
     * @return Response
     */
    public function rejectInviteAction(): Response
    {
        return new Response('Вы отказались вступить в группу');
    }
}