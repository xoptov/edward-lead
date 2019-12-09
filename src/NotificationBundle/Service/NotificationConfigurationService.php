<?php

namespace NotificationBundle\Service;

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
        $this->setUserNotificationConfiguration();
        $this->validator = $validator;
    }

    /**
     * @return array
     */
    public function getViewData(): array
    {
        $notificationCases = Cases::CASE_CHANNELS;

        foreach ($notificationCases as $caseName => $channels) {

            foreach ($channels as &$channel) {
                $channel = [
                    $channel => $this->getConfiguration($caseName, $channel)
                ];
            }
        }
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
        $currentUser = $this->security->getUser();
        $this->userNotificationConfiguration = $this->notificationConfigurationRepository->find(['user' => $currentUser]);
    }
}