<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PhoneCall;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TelephonyController extends Controller
{
    /**
     * @Route("/telephony/calls", name="app_telephony_calls", methods={"GET"})
     *
     * @return Response
     */
    public function callListAction(): Response
    {
        $user = $this->getUser();

        $phoneCalls = $this->getDoctrine()
            ->getRepository(PhoneCall::class)
            ->findBy(['caller' => $user], ['createdAt' => 'DESC']);

        return $this->render('@App/Telephony/lead_call_list.html.twig', [
            'phoneCalls' => $phoneCalls
        ]);
    }
}