<?php

namespace NotificationBundle\Channels;

use NotificationBundle\Client\TelegramClient;
use NotificationBundle\Repository\NotificationConfigurationRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

class TelegramChannel extends BaseChannel
{
    const NAME = 'TELEGRAM_CHANNEL';

    public function __construct(
        NotificationConfigurationRepository $notificationStatusRepository,
        Security $security,
        LoggerInterface $logger,
        TelegramClient $client
    )
    {
        parent::__construct($notificationStatusRepository, $security, $logger, $client);
    }
}