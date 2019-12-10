<?php

namespace NotificationBundle\Controller;

use AppBundle\Entity\User;
use Exception;
use NotificationBundle\Repository\NotificationRepository;
use NotificationBundle\Service\DisableNotificationService;
use NotificationBundle\Service\NotificationConfigurationService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends Controller
{
    /**
     * @var NotificationRepository
     */
    private $notificationRepository;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var NotificationConfigurationService
     */
    private $configurationService;

    /**
     * @var DisableNotificationService
     */
    private $disableNotificationService;

    /**
     * NotificationMenuController constructor.
     *
     * @param NotificationRepository           $notificationRepository
     * @param Security                         $security
     * @param NotificationConfigurationService $configurationService
     * @param DisableNotificationService       $disableNotificationService
     */
    public function __construct(
        NotificationRepository $notificationRepository,
        Security $security,
        NotificationConfigurationService $configurationService,
        DisableNotificationService $disableNotificationService
    )
    {
        $this->notificationRepository = $notificationRepository;
        $this->security = $security;
        $this->configurationService = $configurationService;
        $this->disableNotificationService = $disableNotificationService;
    }

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

    public function renderPopupAction(): Response
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $notifications = $this->notificationRepository->getForUser($user);
        $notificationCount = $this->notificationRepository->getNewCountForUser($user);

        return $this->render(
            '@NotificationBundle/notification_menu.html.twig',
            [
                'notifications' => $notifications,
                'notificationCount' => $notificationCount
            ]
        );
    }

    /**
     * @Route("notifications/switch", methods={"POST"}, name="notifications_switch")
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws Exception
     */
    public function switchNotificationAction(Request $request): Response
    {
        foreach ($request->request->all() as $key => $value){
            $this->disableNotificationService->handle($key, $value);
        }
        return $this->redirectToRoute('app_profile');
    }
}
