<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

class SecurityController extends Controller
{
    /**
     * @Route("/profile/sessions/clear", name="app_sessions_clear", methods={"GET"})
     *
     * @param SessionStorageInterface $sessionStorage
     * @return Response
     */
    public function endActiveSessionsAction(SessionStorageInterface $sessionStorage)
    {
        $sessionStorage->clear();

        return new Response('All active session is cleared!');
    }
}