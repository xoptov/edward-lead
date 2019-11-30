<?php

namespace NotificationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class NotificationController extends Controller
{
    /**
     * @Route("/notifications", methods={"GET"})
     */
    public function indexAction(): Response
    {
        // TODO return list of notifications

        return new Response();
    }

    /**
     * @Route("/notifications/unread/{id}", methods={"PATCH"})
     *
     * @param int $id
     *
     * @return Response
     */
    public function unreadAction(int $id): Response
    {
        // TODO set message by id unread

        return new Response();
    }

    /**
     * @Route("/notifications", methods={"POST"})
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
     * @Route("/notifications/switch", methods={"PATCH"})
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