<?php

namespace NotificationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends Controller
{

    /**
     * @Route("/notifiactions", methods={"GET"})
     */
    public function indexAction()
    {
        // TODO return list of notifications
    }

    /**
     * @Route("/notifiactions/unread/{id}", methods={"PATCH"})
     * @param int $id
     */
    public function unreadAction(int $id)
    {

        // TODO set message by id unread

    }

    /**
     * @Route("/notifiactions", methods={"POST"})
     * @param Request $request
     */
    public function massSendAction(Request $request)
    {
        // TODO make mass send
    }

    /**
     * @Route("/notifiactions/switch", methods={"PATCH"})
     * @param Request $request
     */
    public function switchNotificationAction(Request $request)
    {
        // TODO enable or disable Notification
    }
}
