<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Entity\City;
use AppBundle\Entity\Lead;
use AppBundle\Entity\User;
use AppBundle\Util\Formatter;
use AppBundle\Entity\Company;
use AppBundle\Entity\Account;
use AppBundle\Entity\Property;
use AppBundle\Event\LeadEvent;
use AppBundle\Form\Type\LeadType;
use AppBundle\Service\LeadManager;
use AppBundle\Service\TimerManager;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Security\Voter\LeadEditVoter;
use AppBundle\Security\Voter\LeadViewVoter;
use AppBundle\Security\Voter\LeadCreateVoter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Form\DataTransformer\NumberToBooleanTransformer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @Route("/api/v1")
 */
class LeadController extends APIController
{
    /**
     * @var LeadManager
     */
    private $leadManager;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TimerManager
     */
    private $timerManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @param LeadManager            $leadManager
     * @param TimerManager           $timerManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        LeadManager $leadManager,
        TimerManager $timerManager
    ) {
        $this->entityManager = $entityManager;
        $this->leadManager = $leadManager;
        $this->timerManager = $timerManager;
    }

    /**
     * @Route("/lead/{id}", name="api_v1_lead_view", methods={"GET"}, defaults={"_format": "json"})
     *
     * @param Lead $lead
     *
     * @return JsonResponse
     */
    public function getViewAction(Lead $lead): JsonResponse
    {
        if (!$this->isGranted(LeadViewVoter::OPERATION, $lead)) {
            return new JsonResponse([
                'error' => 'У Вас нет прав на просмотр информации по указанному лиду'
            ], Response::HTTP_FORBIDDEN);
        }

        $numToBoolTransformer = new NumberToBooleanTransformer();

        /** @var User $user */
        $user = $this->getUser();

        $result = [
            'id' => $lead->getId(),
            'phone' => $this->leadManager->getNormalizedPhone($lead, $user),
            'name' => $lead->getName() ? $lead->getName() : 'Неизвестно',
            'orderDate' => $lead->getOrderDateFormatted('c'),
            'decisionMaker' => $numToBoolTransformer->transform($lead->isDecisionMaker()),
            'interestAssessment' => $lead->getInterestAssessment(),
            'description' => $lead->getDescription(),
            'audioRecord' => $lead->getAudioRecord(),
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

        $now = $this->timerManager->createDateTime();

        if ($lead->isExpected()
            && $lead->hasTimer()
            && $lead->getTimerEndAt()
            && $lead->getTimerEndAt() > $now
        ) {
            $remainInSeconds = Formatter::intervalInSeconds($now, $lead->getTimer()->getEndAt());
            $result['timer'] = [
                'remain' => Formatter::humanTimerRemain($remainInSeconds)
            ];
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/lead/form/settings", name="api_v1_lead_form_settings", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getFormSettings(): JsonResponse
    {
        $result = [];

        $cities = $this->entityManager->getRepository(City::class)
            ->findBy(['enabled' => true]);

        if (!empty($cities)) {
            $result['cities'] = [];
            foreach ($cities as $city) {
                $result['cities'][] = [
                    'id' => $city->getId(),
                    'name' => $city->getName()
                ];
            }
        }

        $channels = $this->entityManager->getRepository(Property::class)
            ->findBy(['type' => Property::CHANNEL]);

        if (!empty($channels)) {
            $result['channels'] = [];
            foreach ($channels as $channel) {
                $result['channels'][] = [
                    'id' => $channel->getId(),
                    'value' => $channel->getValue()
                ];
            }
        }

        $result['audioAllowedTypes'] = $this->getParameter('audio_allowed_types');
        $result['audioMaxSize'] = $this->getParameter('audio_max_size');

        return new JsonResponse($result);
    }

    /**
     * @Route("/lead", name="api_v1_lead_create", methods={"POST"}, defaults={"_format": "json"})
     *
     * @param Request                  $request
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return Response
     */
    public function postCreateAction(
        Request $request,
        EventDispatcherInterface $eventDispatcher
    ): Response {

        if (!$this->isGranted('ROLE_WEBMASTER')) {
            return new JsonResponse(['error' => 'Вы должны быть вэбмастером для того чтобы создавать лидов'], Response::HTTP_FORBIDDEN);
        }

        $form = $this->createForm(LeadType::class, null, [
            'csrf_protection' => false
        ]);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->responseErrors($form->getErrors(true));
        }

        /** @var User $user */
        $user = $this->getUser();

        if (!$this->leadManager->checkActiveLeadPerUser($user)) {
            return new JsonResponse([
                'error' => 'Привышено количество активных лидов для пользователя'
            ], Response::HTTP_BAD_REQUEST);
        }

        /** @var Lead $newLead */
        $newLead = $form->getData();
        $newLead->setUser($user);

        if (!$this->isGranted(LeadCreateVoter::OPERATION, $newLead)) {
            return new JsonResponse(['error' => 'Вы не имеете прав создавать нового лида'], Response::HTTP_FORBIDDEN);
        }

        $this->leadManager->postCreate($newLead);

        $this->entityManager->persist($newLead);
        $this->entityManager->flush();

        $eventDispatcher->dispatch(LeadEvent::NEW_PLACED, new LeadEvent($newLead));

        return new JsonResponse(['id' => $newLead->getId()]);
    }

    /**
     * @Route("/lead/estimate", name="api_v1_lead_estimate", methods={"POST"}, defaults={"_format": "json"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function postEstimateAction(Request $request): JsonResponse
    {
        if (!$this->isGranted('ROLE_WEBMASTER')) {
            return new JsonResponse(['error' => 'Вы должны быть вэбмастером для получения оценки лида'], Response::HTTP_FORBIDDEN);
        }

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

        return new JsonResponse(['error' => 'Не удалось расчитать стоимость']);
    }

    /**
     * @Route("/lead/{id}", name="api_v1_lead_update", methods={"PUT"}, defaults={"_format": "json"})
     *
     * @param Lead                     $lead
     * @param Request                  $request
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return JsonResponse
     */
    public function putUpdateAction(
        Lead $lead,
        Request $request,
        EventDispatcherInterface $eventDispatcher
    ): Response {

        if (!$this->isGranted(LeadEditVoter::OPERATION, $lead)) {
            return new JsonResponse(['error' => 'У Вас нет прав на редактирование чужего лида'], Response::HTTP_FORBIDDEN);
        }

        $form = $this->createForm(LeadType::class, $lead, [
            'method' => REquest::METHOD_PUT,
            'csrf_protection' => false
        ]);

        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->responseErrors($form->getErrors(true));
        }

        $this->entityManager->flush();

        $eventDispatcher->dispatch(LeadEvent::EDITED, new LeadEvent($lead));

        return new JsonResponse(['id' => $lead->getId()]);
    }

    /**
     * @Route("/leads/{room}", name="api_v1_leads", methods={"GET"}, defaults={"_format":"json", "room": null})
     *
     * @param string $room
     *
     * @return JsonResponse
     */
    public function getOffersAction(?string $room = null): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $repository = $this->entityManager->getRepository(Lead::class);

        if ($room) {
            $leads = $repository->getOffersByRooms([$room], [
                Lead::STATUS_EXPECT,
                Lead::STATUS_IN_WORK
            ]);
        } elseif ($user->isCompany() && $user->hasCompany()) {
            $company = $user->getCompany();
            $leads = $repository->getOffersByCities($company->getCities()->toArray(), [Lead::STATUS_EXPECT]);
        } else {
            $leads = $repository->getOffers([Lead::STATUS_EXPECT]);
        }

        $result = [];

        /** @var Lead $lead */
        foreach ($leads as $lead) {
            $leadUser = $lead->getUser();

            $row = [
                'id' => $lead->getId(),
                'created_at' => $lead->getCreatedAtTimestamp(),
                'phone' => $this->leadManager->getNormalizedPhone($lead, $user),
                'user' => [
                    'id' => $leadUser->getId(),
                    'name' => $leadUser->getName()
                ],
                'stars' => $this->leadManager->estimateStars($lead),
                'city' => $lead->getCityName(),
                'cpa' => false,
                'audioRecord' => $lead->hasAudioRecord(),
                'channel' => $lead->getChannelName(),
                'status' => $lead->getStatus(),
                'price' => $lead->getPrice(Account::DIVISOR)
            ];

            if ($lead->hasTrade()) {
                $trade = $lead->getTrade();
                /** @var User $buyer */
                $buyer = $trade->getBuyer();
                $row['buyer'] = [
                    'id' => $buyer->getId(),
                    'name' => $buyer->getName()
                ];
                /** @var Company $company */
                $company = $buyer->getCompany();
                if ($company) {
                    $row['buyer']['company'] = [
                        'id' => $company->getId(),
                        'shortName' => $company->getShortName(),
                        'largeName' => $company->getLargeName()
                    ];
                }
            }

            $now = $this->timerManager->createDateTime();

            if ($lead->isExpected()
                && $lead->hasTimer()
                && $lead->getTimerEndAt()
                && $lead->getTimerEndAt() > $now
            ) {
                $remainInSeconds = Formatter::intervalInSeconds($now, $lead->getTimer()->getEndAt());
                $row['timer'] = [
                    'remain' => Formatter::humanTimerRemain($remainInSeconds)
                ];
            }
    
            $result[] = $row;
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/lead/{id}/archive", name="api_v1_archive_lead", methods={"GET"}, defaults={"_format": "json"})
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param Lead                     $lead
     *
     * @return JsonResponse
     */
    public function archiveAction(
        EventDispatcherInterface $eventDispatcher,
        Lead $lead
    ): JsonResponse {

        if (!$this->isGranted(LeadEditVoter::OPERATION, $lead)) {
            return new JsonResponse([
                'error' => 'У вас нет прав на архивирование лида'
            ], Response::HTTP_FORBIDDEN);
        }

        if (!$lead->isExpected()) {
            return new JsonResponse([
                'error' => 'Нельзя отправлять лида в архив с другими статусами кроме "ожидает"'
            ], Response::HTTP_BAD_REQUEST);
        }

        $lead->setStatus(Lead::STATUS_ARCHIVE);
        $this->entityManager->flush();

        $this->addFlash('success', 'Лид #' . $lead->getId() . ' помещен в архив');

        $eventDispatcher->dispatch(LeadEvent::ARCHIVED, new LeadEvent($lead));

        return new JsonResponse(['id' => $lead->getId(), 'status' => Lead::STATUS_ARCHIVE]);
    }
}
