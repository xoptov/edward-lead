<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\CallbackType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TelephonyController extends Controller
{
    /**
     * @Route("/telephony/callback", name="app_telephony_callback", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function callbackAction(Request $request): JsonResponse
    {
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