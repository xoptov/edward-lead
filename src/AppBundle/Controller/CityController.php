<?php

namespace AppBundle\Controller;

use AppBundle\Entity\City;
use AppBundle\Entity\Region;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CityController extends Controller
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
     * @Route("/cities", name="app_cities", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getAllAction(): JsonResponse
    {
        $cities = $this->entityManager->getRepository(City::class)
            ->findBy(['enabled' => true]);

        $result = [];

        foreach ($cities as $city) {
            $item = [
                'id' => $city->getId(),
                'name' => $city->getName(),
                'leadPrice' => $city->getLeadPrice(),
                'startPrice' => $city->getStarPrice()
            ];

            if ($city->hasRegion()) {
                $item['region'] = [
                    'id' => $city->getRegionId(),
                    'name' => $city->getRegionName()
                ];
            } else {
                $item['region'] = null;
            }

            $result[] = $item;
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/cities/{region}", name="app_cities_by_region", methods={"GET"})
     *
     * @param Region $region
     *
     * @return JsonResponse
     */
    public function getByRegionAction(Region $region): JsonResponse
    {
        $cities = $this->entityManager->getRepository(City::class)
            ->findBy(['region' => $region, 'enabled' => true]);

        $result = [];

        foreach ($cities as $city) {
            $result[] = [
                'id' => $city->getId(),
                'name' => $city->getName(),
                'leadPrice' => $city->getLeadPrice(),
                'startPrice' => $city->getStarPrice()
            ];
        }

        return new JsonResponse($result);
    }
}