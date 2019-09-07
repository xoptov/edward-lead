<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PaymentController extends Controller
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface   $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/app/status", name="api_app_status", methods={"GET"}, defaults={"_format":"json"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function apiAppStatusAction(Request $request): JsonResponse
    {
        $invoice = $this->entityManager
            ->getRepository(Invoice::class)
            ->getByHash('123');
        return new JsonResponse(['code' => 0, 'response' => 'ok', 'result' => null]);
    }
}