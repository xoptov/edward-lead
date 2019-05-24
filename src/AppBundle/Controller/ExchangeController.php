<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Account;
use AppBundle\Entity\Lead;
use AppBundle\Entity\User;
use AppBundle\Event\LeadEvent;
use AppBundle\Form\Type\LeadType;
use AppBundle\Service\LeadManager;
use AppBundle\Service\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ExchangeController extends Controller
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
     * @Route("/exchange", name="app_exchange", methods={"GET"})
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('@App/Exchange/index.html.twig');
    }

    /**
     * @Route("/exchange/my-leads", name="app_exchange_my_leads", methods={"GET"})
     *
     * @return Response
     */
    public function myLeadsAction(): Response
    {
        return $this->render('@App/Exchange/my_leads.html.twig');
    }

    /**
     * @Route("/exchange/lead/create", name="app_exchange_create_lead", methods={"GET", "POST"})
     *
     * @param Request                  $request
     * @param Uploader                 $uploader
     * @param LeadManager              $leadManager
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return Response
     */
    public function createLeadAction(
        Request $request,
        Uploader $uploader,
        LeadManager $leadManager,
        EventDispatcherInterface $eventDispatcher
    ): Response {

        $form = $this->createForm(LeadType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var Lead $data */
            $data = $form->getData();

            /** @var User $user */
            $user = $this->getUser();
            $data->setUser($user);

            $existed = $this->entityManager->getRepository(Lead::class)->findBy([
                'phone' => $data->getPhone(),
                'status' => Lead::STATUS_ACTIVE
            ]);

            if (count($existed)) {
                $this->addFlash('error', 'Лид с указанным телефонам уже торгуется на бирже');

                return $this->render('@App/Exchange/create_lead.html.twig', [
                    'form' => $form->createView()
                ]);
            }

            $leadManager->setExpirationDate($data);

            try {
                $activeLeadsCount = $this->entityManager
                    ->getRepository(Lead::class)
                    ->getActiveCountByUser($user);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Произошла ошибка при создании лида');

                return $this->render('@App/Exchange/create_lead.html.twig', ['form' => $form->createView()]);
            }

            if ($user->getSaleLeadLimit()) {
                $leadLimit = $user->getSaleLeadLimit();
            } else {
                $leadLimit = $this->container->getParameter('lead_per_user');
            }

            if ($activeLeadsCount >= $leadLimit) {
                $this->addFlash('error', 'Превышено количество активных лидов на бирже');

                return $this->render('@App/Exchange/create_lead.html.twig', ['form' => $form->createView()]);
            }

            if ($data->getUploadedAudioRecord()) {
                $fileStorePath = $uploader->store($data->getUploadedAudioRecord(), Uploader::DIRECTORY_AUDIO);
                $data->setAudioRecord($fileStorePath);
            }

            $leadManager->setExpirationDate($data);
            $data->setPrice($leadManager->calculateCost($data));

            $this->entityManager->persist($data);
            $this->entityManager->flush();

            $eventDispatcher->dispatch(LeadEvent::NEW_LEAD_PLACED, new LeadEvent($data));

            $this->addFlash('success', 'Лид размещён на бирже');

            return $this->redirectToRoute('app_exchange_my_leads');
        }

        return $this->render('@App/Exchange/create_lead.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/exchange/calculate-lead-cost", name="app_exchange_calculate_lead_cost", methods={"POST"}, defaults={"_format"="json"})
     *
     * @param Request     $request
     * @param LeadManager $leadManager
     *
     * @return JsonResponse
     */
    public function calculateLeadCostAction(Request $request, LeadManager $leadManager): JsonResponse
    {
        $form = $this->createForm(LeadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            /** @var Lead $data */
            $data = $form->getData();
            $stars = $leadManager->calculateStars($data);

            return new JsonResponse(['stars' => $stars, 'cost' => $leadManager->calculateCost($data, Account::DIVISOR)]);
        }

        $errors = [];

        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        return new JsonResponse(['error' => $errors], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/exchange/leads", name="app_exchange_leads", methods={"GET"}, defaults={"_format"="json"})
     *
     * @param LeadManager $leadManager
     *
     * @return JsonResponse
     */
    public function getOffersAction(LeadManager $leadManager): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $company = $user->getCompany();

        if ($company) {
            $cities = $company->getCities();

            $leads = $this->entityManager->getRepository(Lead::class)
                ->getByActiveAndCities($cities->toArray());
        } else {
            $leads = $this->entityManager->getRepository(Lead::class)
                ->getByActive();
        }

        if (empty($leads)) {
            return new JsonResponse(['message' => 'Для Ваших городов нет выставленных лидов на продажу']);
        }

        $result = [];

        foreach ($leads as $lead) {
            $result[] = [
                'id' => $lead->getId(),
                'created_at' => $lead->getCreatedAt()->getTimestamp() * 1000,
                'stars' => $leadManager->calculateStars($lead),
                'city' => $lead->getCity()->getName(),
                'cpa' => false,
                'audio_record' => $lead->hasAudioRecord(),
                'channel' => $lead->getChannelName(),
                'price' => $lead->getPrice() / Account::DIVISOR
            ];
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/exchange/lead/{id}", name="app_exchange_show_lead", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param Lead $lead
     *
     * @return Response
     */
    public function showLeadAction(Lead $lead)
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($lead->getUser()->getId() == $user->getId()) {
            return $this->render("@App/Exchange/show_lead_before.html.twig", ["lead" => $lead]);
        } else {
            // ToDo сделать другую вьюху задача FNn0dBwD
            return $this->render("@App/Exchange/show_lead_before.html.twig", ["lead" => $lead]);
        }
    }
}