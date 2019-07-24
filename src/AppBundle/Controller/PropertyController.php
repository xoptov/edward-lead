<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Property;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PropertyController extends Controller
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/properties/{type}", name="app_properties_by_type", methods={"GET"})
     *
     * @param string $type
     *
     * @return JsonResponse
     */
    public function getByTypeAction(string $type): JsonResponse
    {
        $properties = $this->entityManager->getRepository(Property::class)
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