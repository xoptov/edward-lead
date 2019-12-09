<?php

namespace NotificationBundle\Channels;

use AppBundle\Entity\User;
use NotificationBundle\Client\Client;
use NotificationBundle\Entity\NotificationConfiguration;
use NotificationBundle\Repository\NotificationConfigurationRepository;
use NotificationBundle\Repository\NotificationStatusRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

abstract class BaseChannel
{
    /**
     * @var NotificationConfigurationRepository
     */
    private $notificationConfigurationRepository;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * BaseChannel constructor.
     *
     * @param NotificationConfigurationRepository $notificationConfigurationRepository
     * @param Security                            $security
     * @param LoggerInterface                     $logger
     * @param Client                              $client
     */
    public function __construct(
        NotificationConfigurationRepository $notificationConfigurationRepository,
        Security $security,
        LoggerInterface $logger,
        Client $client
    )
    {
        $this->notificationConfigurationRepository = $notificationConfigurationRepository;
        $this->security = $security;
        $this->logger = $logger;
        $this->client = $client;
    }

    /**
     * @param array       $data
     * @param string|null $case
     */
    public function send(array $data, string $case = null): void
    {
        if ($case && !$this->isAllowed($case, self::NAME)) {
            return;
        }

        try {
            $this->client->send($data);
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());
        }
    }

    /**
     * @param string $case
     * @param string $channel
     *
     * @return bool
     */
    protected function isAllowed(string $case, string $channel): bool
    {
        /** @var User $user */
        $user = $this->security->getUser();

        /** @var NotificationConfiguration|null $result */
        $result = $this->notificationConfigurationRepository->findByConfigurations($case, $channel, $user);

        return $result && $result->isDisabled() ? false : true;

    }
}