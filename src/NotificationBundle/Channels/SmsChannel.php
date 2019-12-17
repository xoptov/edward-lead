<?php

namespace NotificationBundle\Channels;

use NotificationBundle\Client\SmsRuClient;
use NotificationBundle\Repository\NotificationConfigurationRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

class SmsChannel extends BaseChannel
{
    const NAME = 'WEB_PUSH_CHANNEL';

    public function __construct(
        NotificationConfigurationRepository $notificationStatusRepository,
        Security $security,
        LoggerInterface $logger,
        SmsRuClient $client
    )
    {
        parent::__construct($notificationStatusRepository, $security, $logger, $client);
    }

    /**
     * @param array       $data
     * @param string|null $case
     */
    public function send(array $data, string $case = null): void
    {
        if ($case && !$this->isAllowed($case, static::NAME)) {
            return;
        }

        try {
            $result = $this->client->send($data);
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());
        }
    }
}