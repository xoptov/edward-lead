<?php

namespace NotificationBundle\Channels;

use NotificationBundle\Client\TelegramClient;
use NotificationBundle\Repository\NotificationStatusRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

class TelegramChannel extends BaseChannel
{
    const NAME = 'TELEGRAM_CHANNEL';

    public function __construct(
        NotificationStatusRepository $notificationStatusRepository,
        Security $security,
        LoggerInterface $logger,
        TelegramClient $client
    )
    {
        parent::__construct($notificationStatusRepository, $security, $logger, $client);
    }
}