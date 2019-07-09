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
            $roomManager->updateInviteToken($data);

            $this->entityManager->persist($data);
            $this->entityManager->flush();

            $this->addFlash('success', 'Новая комната успешно создана');

            return $this->render('@App/Default/index.html.twig');
        }

        return $this->render('@App/Room/create.html.twig', ['form' => $form->createView()]);
    }
}