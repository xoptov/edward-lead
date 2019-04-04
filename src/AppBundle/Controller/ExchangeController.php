<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ExchangeController extends Controller
{
    /**
     * @Route("/exchange", name="app_exchange", methods={"GET"})
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('@App/Exchange/index.html.twig');
    }

    /**
     * @Route("/exchange/my-leads", name="app_exchange_my_leads", methods={"GET"})
     *
     * @return Response
     */
    public function myLeadsAction(): Response
    {
        return $this->render('@App/Exchange/my_leads.html.twig');
    }
}