<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Lead;
use AppBundle\Entity\User;
use AppBundle\Entity\Account;
use AppBundle\Event\LeadEvent;
use AppBundle\Form\Type\LeadType;
use AppBundle\Service\LeadManager;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Security\Voter\LeadEditVoter;
use AppBundle\Security\Voter\LeadViewVoter;
use AppBundle\Security\Voter\LeadCreateVoter;
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
     * @var LeadManager
     */
    private $leadManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @param LeadManager            $leadManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        LeadManager $leadManager
    ) {
        $this->entityManager = $entityManager;
        $this->leadManager = $leadManager;
    }

    /**
     * @Route("/lead/form/{lead}", name="app_lead_form", methods={"GET"}, defaults={"lead": null})
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
     * @param Lead $lead
     *
     * @return JsonResponse
     */
    public function viewAction(Lead $lead): JsonResponse
    {
        if (!$this->isGranted(LeadViewVoter::OPERATION, $lead)) {
            return new JsonResponse([
                'errors' => 'У Вас нет прав на просмотр информации по указанному лиду'
            ], Response::HTTP_FORBIDDEN);
        }

        $result = [
            'id' => $lead->getId(),
            'phone' => $this->leadManager->getNormalizedPhone($lead, $this->getUser()),
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
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return Response
     */
    public function createAction(
        Request $request,
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

        if (!$this->leadManager->checkActiveLeadPerUser($user)) {
            return new JsonResponse([
                'errors' => 'Привышено количество активных лидов для пользователя'
            ], Response::HTTP_BAD_REQUEST);
        }

        /** @var Lead $newLead */
        $newLead = $form->getData();
        $newLead
            ->setUser($user)
            ->setPrice($this->leadManager->estimateCost($newLead));

        $this->leadManager->setExpirationDate($newLead);

        if (!$this->isGranted(LeadCreateVoter::OPERATION)) {
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
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function estimateAction(Request $request): JsonResponse
    {
        $form = $this->createForm(LeadType::class, null, [
            'csrf_protection' => false
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            /** @var Lead $data */
            $data = $form->getData();

            return new JsonResponse([
                'stars' => $this->leadManager->estimateStars($data),
                'cost' => $this->leadManager->estimateCost($data, Account::DIVISOR)
            ]);
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

        if (!$this->isGranted(LeadEditVoter::OPERATION, $lead)) {
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