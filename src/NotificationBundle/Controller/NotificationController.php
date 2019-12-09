<?php

namespace NotificationBundle\Controller;

use NotificationBundle\Service\NotificationConfigurationService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends Controller
{

    /**
     * @var NotificationConfigurationService
     */
    private $configurationService;

    /**
     * NotificationController constructor.
     *
     * @param NotificationConfigurationService $configurationService
     */
    public function __construct(NotificationConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
    }

    /**
     * @Route("/notifications", methods={"GET"})
     */
    public function indexAction(): Response
    {

        $notificationConfiguration = $this->configurationService->getViewData();

        return $this->render(
            '@NotificationBundle/notification.html.twig',
            [
                'notificationConfiguration' => $notificationConfiguration,
            ]
        );
    }
}
