<?php

namespace NotificationBundle\Controller\Api;

use Exception;
use NotificationBundle\Service\WebPushTokenHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebPushController extends Controller
{
    /**
     * @var WebPushTokenHandler
     */
    private $handler;

    /**
     * WebPushController constructor.
     *
     * @param WebPushTokenHandler $handler
     */
    public function __construct(WebPushTokenHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @Route("api/notifications/push", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws Exception
     */
    public function indexAction(Request $request): Response
    {
        $this->handler->handle($request->request->all());

        return new Response();
    }
}