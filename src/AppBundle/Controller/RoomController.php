<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Room;
use AppBundle\Form\Type\RoomType;
use AppBundle\Service\RoomManager;
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
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/room/create", name="app_create_room", methods={"GET", "POST"})
     *
     * @param Request     $request
     * @param RoomManager $roomManager
     *
     * @return Response
     */
    public function createAction(Request $request, RoomManager $roomManager): Response
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
                $roomManager->joinInRoom($data, $this->getUser());
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());

                return $this->redirectToRoute('app_profile');
            }

            $roomManager->updateInviteToken($data);
            $this->entityManager->flush();

            $this->addFlash('success', 'Новая комната успешно создана');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('@App/Room/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/room/invite/{token}", name="app_invite_room", methods={"GET"})
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
     * @Route("/invite/accept/{token}", name="app_accept_invite", methods={"GET"})
     *
     * @param string                 $token
     * @param RoomManager            $roomManager
     *
     * @return Response
     */
    public function acceptInviteAction(string $token, RoomManager $roomManager): Response
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
            $roomManager->joinInRoom($room, $this->getUser());
            $roomManager->updateInviteToken($room);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return $this->redirectToRoute('app_profile');
        }

        return new Response('Вы вступили в приглашённую группу');
    }

    /**
     * @Route("/invite/reject", name="app_reject_invite", methods={"GET"})
     *
     * @return Response
     */
    public function rejectInviteAction(): Response
    {
        return new Response('Вы отказались вступить в группу');
    }
}