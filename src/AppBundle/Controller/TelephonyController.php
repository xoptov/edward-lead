<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Lead;
use Psr\Log\LoggerInterface;
use GuzzleHttp\ClientInterface;
use AppBundle\Form\Type\CallbackType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TelephonyController extends Controller
{
    /**
     * @Route("/telephony/make-call/{lead}", name="app_telephony_make_call", methods={"GET"})
     *
     * @param ClientInterface $client
     * @param Lead            $lead
     *
     * @return Response
     */
    public function makeCallAction(ClientInterface $client, Lead $lead): Response
    {
        if (!$this->isGranted('ROLE_COMPANY')) {
            return new JsonResponse(
                ['message' => 'Только пользователи компании могут делать звноки'],
                Response::HTTP_BAD_REQUEST
            );
        }

        if (!$lead->isActive()) {
            return new JsonResponse(
                ['message' => 'Звонок можно совершить только активному лиду'],
                Response::HTTP_BAD_REQUEST
            );
        }

        return new Response('ok!');
    }

    /**
     * @Route("/telephony/callback", name="app_telephony_callback", methods={"POST"})
     *
     * @param Request         $request
     * @param LoggerInterface $logger
     *
     * @return Response
     */
    public function callbackAction(Request $request, LoggerInterface $logger): Response
    {
//        $logger->debug('Callback from Asterisk', $request->request->all());
//        return new Response('Request received!');

        $form = $this->createForm(CallbackType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
            return new JsonResponse(['message' => 'Данные о звонке успешно приняты']);
        }

        $errors = [];

        foreach ($form->getErrors(true) as $error) {
            $errors[] = [
                'field' => $error->getOrigin()->getName(),
                'message' => $error->getMessage()
            ];
        }

        return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
    }
}