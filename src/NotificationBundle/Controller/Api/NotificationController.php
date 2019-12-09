<?php

namespace NotificationBundle\Controller\Api;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use NotificationBundle\Entity\Notification;
use NotificationBundle\Service\DisableNotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends Controller
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var DisableNotificationService
     */
    private $disableNotificationService;

    /**
     * NotificationController constructor.
     *
     * @param EntityManagerInterface     $entityManager
     * @param DisableNotificationService $disableNotificationService
     */
    public function __construct(EntityManagerInterface $entityManager, DisableNotificationService $disableNotificationService)
    {
        $this->entityManager = $entityManager;
        $this->disableNotificationService = $disableNotificationService;
    }

    /**
     * @Route("/api/v1/notifications/read/{notification}", methods={"GET"})
     *
     * @param Notification $notification
     *
     * @return Response
     */
    public function readAction(Notification $notification): Response
    {
        $notification->setReadStatus(Notification::READ_STATUS_VIEWED);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        return new Response();
    }


    /**
     * @Route("/api/v1/notifications/unread/{notification}", methods={"GET"})
     *
     * @param Notification $notification
     *
     * @return Response
     */
    public function unreadAction(Notification $notification): Response
    {
        $notification->setReadStatus(Notification::READ_STATUS_NEW);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        return new Response();
    }

    /**
     * @Route("/api/v1/notifications", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function massSendAction(Request $request): Response
    {
        // TODO make mass send

        return new Response();
    }

    /**
     * @Route("/api/v1/notifications/switch", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function switchNotificationAction(Request $request): Response
    {
        $configuration = $this->disableNotificationService->handle($request->request->all());

        return new Response($configuration);
    }
}
