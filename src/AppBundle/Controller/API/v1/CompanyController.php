<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Security\Voter\CompanyVoter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/api/v1")
 */
class CompanyController extends APIController
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
     * @Route("/company/{id}", name="api_v1_company_view", methods={"GET"})
     *
     * @param Company $company
     *
     * @return JsonResponse
     */
    public function getAction(Company $company): JsonResponse
    {
        if (!$this->isGranted(CompanyVoter::VIEW, $company)) {
            return new JsonResponse(
                ['У вас не прав на просмотр информации о компании'],
                Response::HTTP_FORBIDDEN
            );
        }

        $result = [
            'id' => $company->getId(),
            'user' => $company->getUser() ? ['id' => $company->getUser()->getId()] : null,
            'shortName' => $company->getShortName(),
            'largeName' => $company->getLargeName(),
            'inn' => $company->getInn(),
            'ogrn' => $company->getOgrn(),
            'kpp' => $company->getKpp(),
            'bik' => $company->getBik(),
            'accountNumber' => $company->getAccountNumber(),
            'address' => $company->getAddress(),
            'zipcode' => $company->getZipcode()
        ];

        return new JsonResponse($result);
    }
}