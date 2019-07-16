<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Lead;
use AppBundle\Entity\User;
use AppBundle\Entity\Account;
use AppBundle\Event\LeadEvent;
use AppBundle\Form\Type\LeadType;
use AppBundle\Service\LeadManager;
use AppBundle\Security\Voter\LeadVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class LeadController extends Controller
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
     * @Route("/lead/form/{id}", name="app_lead_form", methods={"GET"}, defaults={"lead": null})
     *
     * @param int|null $id
     *
     * @return Response
     */
    public function getFormAction(?int $id = null): Response
    {
        return $this->render('@App/Lead/form.html.twig', [
            'id' => $id
        ]);
    }


    /**
     * @Route("/lead/{id}", name="app_lead_view", methods={"GET"}, defaults={"_format": "json"})
     *
     * @param Lead        $lead
     * @param LeadManager $leadManager
     *
     * @return JsonResponse
     */
    public function viewAction(Lead $lead, LeadManager $leadManager): JsonResponse
    {
        if (!$this->isGranted(LeadVoter::VIEW, $lead)) {
            return new JsonResponse(['errors' => 'У Вас нет прав на просмотр информации по указанному лиду'], Response::HTTP_FORBIDDEN);
        }

        $result = [
            'id' => $lead->getId(),
            'phone' => $leadManager->getNormalizedPhone($lead, $this->getUser()),
            'name' => $lead->getName(),
            'orderDate' => $lead->getOrderDateFormatted('c'),
            'decisionMaker' => $lead->isDecisionMaker(),
            'madeMeasurement' => $lead->isMadeMeasurement(),
            'interestAssessment' => $lead->getInterestAssessment(),
            'description' => $lead->getDescription(),
            'audioRecord' => $lead->getAudioRecord(),
            'expirationDate' => $lead->getExpirationDateFormatted('c'),
            'status' => $lead->getStatus(),
            'price' => $lead->getPrice(Account::DIVISOR)
        ];

        if ($lead->getRoom()) {
            $result['room'] = [
                'id' => $lead->getRoom()->getId(),
                'name' => $lead->getRoom()->getName()
            ];
        }

        if ($lead->getUser()) {
            $result['user'] = [
                'id' => $lead->getUser()->getId(),
                'name' => $lead->getUser()->getName()
            ];
        }

        if ($lead->getCity()) {
            $result['city'] = [
                'id' => $lead->getCity()->getId(),
                'name' => $lead->getCity()->getName()
            ];
        }

        if ($lead->getChannel()) {
            $result['channel'] = [
                'id' => $lead->getChannel()->getId(),
                'name' => $lead->getChannel()->getValue()
            ];
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/lead", name="app_lead_create", methods={"POST"}, defaults={"_format": "json"})
     *
     * @param Request                  $request
     * @param LeadManager              $leadManager
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return Response
     */
    public function createAction(
        Request $request,
        LeadManager $leadManager,
        EventDispatcherInterface $eventDispatcher
    ): Response {

        if (!$this->isGranted('ROLE_WEBMASTER')) {
            return new JsonResponse(['errors' => 'Вы должны быть вэбмастером для того чтобы создавать лидов'], Response::HTTP_FORBIDDEN);
        }

        $form = $this->createForm(LeadType::class, null, ['csrf_protection' => false]);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->responseErrors($form->getErrors(true));
        }

        /** @var User $user */
        $user = $this->getUser();

        if (!$leadManager->checkActiveLeadPerUser($user)) {
            return new JsonResponse([
                'errors' => 'Привышено количество активных лидов для пользователя'
            ], Response::HTTP_BAD_REQUEST);
        }

        /** @var Lead $newLead */
        $newLead = $form->getData();
        $newLead
            ->setUser($user)
            ->setPrice($leadManager->estimateCost($newLead));

        $leadManager->setExpirationDate($newLead);

        if (!$this->isGranted(LeadVoter::CREATE)) {
            return new JsonResponse(['errors' => 'Вы не имеете прав создавать нового лида'], Response::HTTP_FORBIDDEN);
        }

        $this->entityManager->persist($newLead);
        $this->entityManager->flush();

        $eventDispatcher->dispatch(LeadEvent::NEW_PLACED, new LeadEvent($newLead));

        return new JsonResponse(['id' => $newLead->getId()], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/lead/estimate", name="app_lead_estimate", methods={"POST"}, defaults={"_format"="json"})
     *
     * @param Request     $request
     * @param LeadManager $leadManager
     *
     * @return JsonResponse
     */
    public function estimateAction(Request $request, LeadManager $leadManager): JsonResponse
    {
        $form = $this->createForm(LeadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            /** @var Lead $data */
            $data = $form->getData();
            $stars = $leadManager->estimateStars($data);

            return new JsonResponse(['stars' => $stars, 'cost' => $leadManager->estimateCost($data, Account::DIVISOR)]);
        }

        $errors = [];

        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        return new JsonResponse(['error' => $errors], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/lead/{id}", name="app_lead_edit", methods={"PUT", "PATCH"}, defaults={"_format": "json"})
     *
     * @param Lead                     $lead
     * @param Request                  $request
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return JsonResponse
     */
    public function updateAction(
        Lead $lead,
        Request $request,
        EventDispatcherInterface $eventDispatcher
    ): Response {

        if (!$this->isGranted(LeadVoter::EDIT, $lead)) {
            return new JsonResponse(['errors' => 'У Вас нет прав на редактирование чужего лида'], Response::HTTP_FORBIDDEN);
        }

        $form = $this->createForm(LeadType::class, $lead, ['csrf_protection' => false]);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->responseErrors($form->getErrors(true));
        }

        $this->entityManager->flush();
        $eventDispatcher->dispatch(LeadEvent::EDITED, new LeadEvent($lead));

        return new JsonResponse(['id' => $lead->getId()]);
    }

    /**
     * @param FormErrorIterator $formErrors
     *
     * @return Response
     */
    private function responseErrors(FormErrorIterator $formErrors): Response
    {
        $errors = [];

        foreach ($formErrors as $error) {
            $errors[] = $error->getMessage();
        }

        return new JsonResponse(['errors' => $errors]);
    }
}