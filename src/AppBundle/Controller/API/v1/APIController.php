<?php

namespace AppBundle\Controller\API\v1;

use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class APIController extends Controller
{
    /**
     * @param FormErrorIterator $formErrors
     *
     * @return JsonResponse
     */
    protected function responseErrors(FormErrorIterator $formErrors): JsonResponse
    {
        $errors = [];

        foreach ($formErrors as $error) {
            $origin = $error->getOrigin();

            if (!isset($errors[$origin->getName()])) {
                $errors[$origin->getName()] = [];
            }

            $errors[$origin->getName()][] = $error->getMessage();
        }

        return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }
}