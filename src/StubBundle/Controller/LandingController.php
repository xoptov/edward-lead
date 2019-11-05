<?php

namespace StubBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/stub/landing")
 */
class LandingController extends Controller
{
    /**
     * @Route(name="stub_landing_index", methods={"GET"})
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('@Stub/Landing/index.html.twig');
    }
}