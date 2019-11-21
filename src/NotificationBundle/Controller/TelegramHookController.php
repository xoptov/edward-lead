<?php

namespace NotificationBundle\Controller;

use Exception;
use NotificationBundle\Services\TelegramHookHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TelegramHookController
{
    /**
     * @var TelegramHookHandler
     */
    private $handler;

    /**
     * TelegramHookController constructor.
     * @param TelegramHookHandler $handler
     */
    public function __construct(TelegramHookHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
         * @Route("/notifiactions/telegram/hook", methods={"POST"})
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function indexAction(Request $request)
    {
        $this->handler->handle($request->request->all());
        return new Response();
    }
}