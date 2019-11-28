<?php

namespace NotificationBundle\Controller;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use NotificationBundle\Service\TelegramHookHandler;

class TelegramHookController
{
    /**
     * @var TelegramHookHandler
     */
    private $handler;

    /**
     * @param TelegramHookHandler $handler
     */
    public function __construct(TelegramHookHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @Route("/notifications/telegram/hook", methods={"POST"})
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