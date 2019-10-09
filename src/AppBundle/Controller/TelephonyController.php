<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PhoneCall;
use AppBundle\Security\Voter\PhoneCallVoter;
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

    /**
     * @Route("/telephony/{id}", name="app_telephony_listen_record", methods={"GET"})
     *
     * @param PhoneCall $phoneCall
     *
     * @return Response
     */
    public function listenAudioRecord(PhoneCall $phoneCall): Response
    {
        if (!$this->isGranted(PhoneCallVoter::LISTEN, $phoneCall)) {
            return new Response('У Вас нет прав на прослушивание телефонного разговора');
        }

        return $this->render('@App/Telephony/listen_audio_record.html.twig', [
            'phoneCall' => $phoneCall
        ]);
    }
}