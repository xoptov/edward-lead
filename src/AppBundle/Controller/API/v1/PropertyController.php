<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Entity\Property;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/api/v1")
 */
class PropertyController extends Controller
{
    /**
     * @Route("/properties/{type}", name="api_v1_properties", methods={"GET"})
     *
     * @param string $type
     *
     * @return JsonResponse
     */
    public function getAction(string $type): JsonResponse
    {
        $properties = $this->getDoctrine()->getRepository(Property::class)
            ->findBy(['type' => $type]);

        $result = [];

        /** @var Property $property */
        foreach ($properties as $property) {
            $result[] = [
                'id' => $property->getId(),
                'type' => $property->getType(),
                'value' => $property->getValue()
            ];
        }

        return new JsonResponse($result);
    }
}