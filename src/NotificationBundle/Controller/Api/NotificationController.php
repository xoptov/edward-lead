<?php

namespace NotificationBundle\Controller\Api;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use NotificationBundle\Entity\Notification;
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
     * NotificationController constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/v1/notifications", methods={"GET"})
     */
    public function indexAction(): Response
    {
        // TODO return list of notifications

        return new Response();
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
     * @Route("/api/v1/notifications/switch", methods={"PATCH"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function switchNotificationAction(Request $request): Response
    {
        // TODO enable or disable Notification

        return new Response();
    }
}
