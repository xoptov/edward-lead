<?php

namespace NotificationBundle\Service;

use AppBundle\Entity\User;
use NotificationBundle\Constants\Cases;
use NotificationBundle\Entity\NotificationConfiguration;
use NotificationBundle\Repository\NotificationConfigurationRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NotificationConfigurationService
{
    /**
     * @var notificationConfigurationRepository
     */
    private $notificationConfigurationRepository;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var array
     */
    private $userNotificationConfiguration;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * NotificationController constructor.
     *
     * @param NotificationConfigurationRepository $notificationConfigurationRepository
     * @param Security                            $security
     * @param ValidatorInterface                  $validator
     */
    public function __construct(
        NotificationConfigurationRepository $notificationConfigurationRepository,
        Security $security,
        ValidatorInterface $validator
    )
    {
        $this->notificationConfigurationRepository = $notificationConfigurationRepository;
        $this->security = $security;
        $this->validator = $validator;
    }

    /**
     * @return array
     */
    public function getViewData(): array
    {
        $this->setUserNotificationConfiguration();
        $notificationCases = Cases::CASE_CHANNELS;

        foreach ($notificationCases as $caseName => &$channels) {

            $data = [];
            foreach ($channels as $channel) {
                $data[$channel] = (int)$this->getConfiguration($caseName, $channel);
            }

            $channels = $data;
        }

        return $notificationCases;
    }

    /**
     * @param string $case
     * @param string $channel
     *
     * @return bool
     */
    private function getConfiguration(string $case, string $channel): bool
    {
        /** @var NotificationConfiguration $item */
        foreach ($this->userNotificationConfiguration as $item) {

            if ($item->getCase() === $case && $item->getChannel() === $channel) {
                return $item->isDisabled();
            }
        }
        return true;
    }

    /**
     */
    private function setUserNotificationConfiguration(): void
    {
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();
        $this->userNotificationConfiguration = $this->notificationConfigurationRepository->findBy(['user' => $currentUser]);
    }
}