<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Entity\City;
use AppBundle\Entity\User;
use AppBundle\Entity\Office;
use AppBundle\Util\Formatter;
use AppBundle\Event\OfficeEvent;
use AppBundle\Form\Type\OfficeType;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Security\Voter\OfficeVoter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @Route("/api/v1")
 */
class OfficeController extends APIController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param EntityManagerInterface   $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/office/{id}", name="api_v1_office_view", methods={"GET"})
     *
     * @param Request $request
     * @param Office  $office
     *
     * @return JsonResponse
     */
    public function getAction(Request $request, Office $office): JsonResponse
    {
        if (!$this->isGranted(OfficeVoter::VIEW, $office)) {
            return new JsonResponse(
                ['У вас нет прав на просмотр информации о указаном офисе'],
                Response::HTTP_FORBIDDEN
            );
        }

        $result = [
            'id' => $office->getId(),
            'name' => $office->getName(),
            'phone' => Formatter::humanizePhone($office->getPhone()),
            'address' => $office->getAddress(),
            'cities' => []
        ];

        if ($office->getCities()->isEmpty()) {
            /** @var City $city */
            foreach ($office->getCities() as $city) {
                if (intval($request->get('deep'))) {
                    $result['cities'][] = [
                        'id' => $city->getId(),
                        'name' => $city->getName(),
                        'enabled' => $city->isEnabled()
                    ];
                } else {
                    $result['cities'][] = ['id' => $city->getId()];
                }
            }
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/office", name="api_v1_office_create", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function postAction(Request $request): JsonResponse
    {
        $form = $this->createForm(OfficeType::class, null, [
            'csrf_protection' => false
        ]);

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return new JsonResponse(
                ['Данные не отправлены'],
                Response::HTTP_BAD_REQUEST
            );
        }

        if (!$form->isValid()) {
            return $this->responseErrors($form->getErrors(true));
        }

        /** @var User $user */
        $user = $this->getUser();

        /** @var Office $office */
        $office = $form->getData();
        $office->setUser($user);

        $this->entityManager->persist($office);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(
            OfficeEvent::NEW_CREATED,
            new OfficeEvent($office)
        );

        return new JsonResponse(['id' => $office->getId()]);
    }

    /**
     * @Route("/office/{id}", name="api_v1_office_update", methods={"PUT"})
     *
     * @param Request $request
     * @param Office  $office
     *
     * @return JsonResponse
     */
    public function putAction(Request $request, Office $office): JsonResponse
    {
        if (!$this->isGranted(OfficeVoter::EDIT, $office)) {
            return new JsonResponse(['У вас нет прав на редактирование указанного офиса']);
        }

        $form = $this->createForm(OfficeType::class, $office, [
            'method' => Request::METHOD_PUT,
            'csrf_protection' => false
        ]);

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return new JsonResponse(
                ['Данные не отправлены'],
                Response::HTTP_BAD_REQUEST
            );
        }

        if (!$form->isValid()) {
            return $this->responseErrors($form->getErrors(true));
        }

        $this->entityManager->flush();
        $this->eventDispatcher->dispatch(OfficeEvent::UPDATED, new OfficeEvent($office));

        return new JsonResponse(['id' => $office->getId()]);
    }
}
