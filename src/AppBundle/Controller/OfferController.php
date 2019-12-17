<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/offer")
 */
class OfferController extends Controller
{
    /**
     * @Route("/list", name="app_offer_list")
     * 
     * @param EntityManagerInterface $entityManager
     * 
     * @return Response
     */
    public function listAction(EntityManagerInterface $entityManager): Response
    {
        $rooms = $entityManager->getRepository(Room::class)->findBy([
            'publicOffer' => true
        ]);

        return $this->render('@App/v3/Offer/list.html.twig', [
            'rooms' => $rooms
        ]);
    }
}