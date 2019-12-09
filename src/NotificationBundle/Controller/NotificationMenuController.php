<?php

namespace NotificationBundle\Controller;

use AppBundle\Entity\User;
use NotificationBundle\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class NotificationMenuController extends Controller
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
     * NotificationMenuController constructor.
     *
     * @param NotificationRepository $notificationRepository
     * @param Security               $security
     */
    public function __construct(NotificationRepository $notificationRepository, Security $security)
    {
        $this->notificationRepository = $notificationRepository;
        $this->security = $security;
    }

    public function indexAction(): Response
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
}
