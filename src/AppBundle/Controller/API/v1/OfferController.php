<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use Doctrine\DBAL\DBALException;
use AppBundle\Entity\OfferRequest;
use AppBundle\Entity\RoomJoinRequest;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Repository\OfferRepository;
use FOS\MessageBundle\Sender\SenderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\MessageBundle\Composer\ComposerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\MessageBundle\MessageBuilder\NewThreadMessageBuilder;

/**
 * @Route("/api/v1/offer")
 */
class OfferController extends Controller
{
    /**
     * @var ComposerInterface
     */
    private $composer;

    /**
     * @var SenderInterface
     */
    private $sender;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var int
     */
    private $oneInInterval;

    /**
     * @param ComposerInterface      $composer
     * @param SenderInterface        $sender
     * @param EntityManagerInterface $entityManager
     * @param int                    $oneInInterval
     */
    public function __construct(
        ComposerInterface $composer, 
        SenderInterface $sender, 
        EntityManagerInterface $entityManager,
        int $oneInInterval
    ) {
        $this->composer = $composer;
        $this->sender = $sender;
        $this->entityManager = $entityManager;
        $this->oneInInterval = $oneInInterval;
    }

    /**
     * @Route("/create", name="api_v1_offer_create", methods={"GET"})
     * 
     * @return JsonResponse
     */
    public function createAction(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var OfferRepository $offerRepository */
        $offerRepository = $this->entityManager
            ->getRepository(OfferRequest::class);

        try {
            $nearestOfferRequest = $offerRepository
                ->getCountByUserInInterval($user, $this->oneInInterval);
        } catch (DBALException $e) {
            return new JsonResponse(
                ['Произошла ошибка отправки запроса'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        if ($nearestOfferRequest) {
            return new JsonResponse(
                ['Нельзя слишком часто отправлять запросы'], 
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($user->isAdvertiser()) {
            $messageSubject = 'Запрос на создание нового офера';
            $messageBody = 'Прошу Вас создать оффер';
        } elseif ($user->isWebmaster()) {
            $messageSubject = 'Запрос на подбор рекламодателей';
            $messageBody = 'Прошу Вас подобрать рекламодателей';
        } else {
            return new JsonResponse(
                ['Только рекламодатели или вэбмастеры могут делать запрос на создание офера'],
                Response::HTTP_FORBIDDEN
            );
        }

        /** @var UserRepository */
        $userRepository = $this->entityManager->getRepository(User::class);
        $admins = $userRepository->getAdmins();

        if (empty($admins)) {
            return new JsonResponse(['В системе нет администраторов'], Response::HTTP_BAD_REQUEST);
        }

        /** @var NewThreadMessageBuilder $threadBuilder */
        $threadBuilder = $this->composer->newThread();
        $threadBuilder
            ->setSubject($messageSubject)
            ->setSender($user)
            ->setBody($messageBody);

        foreach ($admins as $admin) {
            $threadBuilder->addRecipient($admin);
        }

        $message = $threadBuilder->getMessage();

        $offerRequest = new OfferRequest();
        $offerRequest->setUser($user);

        $this->entityManager->persist($offerRequest);
        $this->sender->send($message);

        return new JsonResponse(['id' => $message->getId()]);
    }

    /**
     * @Route(
     *  "/{room}/connect-request",
     *  name="api_v1_offer_connect",
     *  methods={"GET"}
     * )
     * 
     * @param Room $room
     * 
     * @return JsonResponse
     */
    public function connectRequestAction(Room $room): JsonResponse
    {
        if (!$this->isGranted('ROLE_WEBMASTER')) {
            return new JsonResponse(
                ['К офферам могут присоединиться только вэбмастера'],
                Response::HTTP_FORBIDDEN
            );
        }

        /** @var User $user */
        $user = $this->getUser();

        if ($room->hasJoinRequest($user)) {
            return new JsonResponse(
                ['Вы уже отправляли запрос на подключение к данной комнате'],
                Response::HTTP_BAD_REQUEST
            );
        }

        /** @var UserRepository */
        $userRepository = $this->entityManager->getRepository(User::class);
        $admins = $userRepository->getAdmins();

        if (empty($admins)) {
            return new JsonResponse(
                ['В системе нет администраторов'], 
                Response::HTTP_BAD_REQUEST
            );
        }

        $messageSubject = 'Запрос на присоединения к оферу';
        $messageBody = 'Прошу Вас присоединить меня к комнате № ' . $room->getId();

        /** @var NewThreadMessageBuilder $threadBuilder */
        $threadBuilder = $this->composer->newThread();
        $threadBuilder
            ->setSubject($messageSubject)
            ->setSender($user)
            ->setBody($messageBody);

        foreach ($admins as $admin) {
            $threadBuilder->addRecipient($admin);
        }

        $message = $threadBuilder->getMessage();

        $joinRequest = new RoomJoinRequest();
        $joinRequest
            ->setUser($user)
            ->setRoom($room);

        $this->entityManager->persist($joinRequest);
        $this->sender->send($message);

        return new JsonResponse(['id' => $message->getId()]);
    }
}