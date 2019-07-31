<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="app_index", methods={"GET"})
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user->isWebmaster()) {
            return $this->redirectToRoute('app_dashboard');
        } elseif ($user->isCompany()) {
            return $this->redirectToRoute('app_room_list');
        }

        return $this->redirectToRoute('app_select_type');
    }
}
