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
        $fieldErrors = [];

        foreach ($formErrors as $error) {

            $origin = $error->getOrigin();

            if (empty($origin) || '' === $origin->getName()) {
                $errors[] = $error->getMessage();
                continue;
            }

            if (!isset($fieldErrors[$origin->getName()])) {
                $fieldErrors[$origin->getName()] = [];
            }

            $fieldErrors[$origin->getName()][] = $error->getMessage();
        }

        $errors[] = $fieldErrors;

        return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
    }
}
