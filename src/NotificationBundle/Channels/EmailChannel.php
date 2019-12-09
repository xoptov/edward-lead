<?php

namespace NotificationBundle\Channels;

use NotificationBundle\Client\EsputnikEmailClient;
use NotificationBundle\Repository\NotificationStatusRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

class EmailChannel extends BaseChannel
{
    const NAME = 'EMAIL_CHANNEL';

    public function __construct(
        NotificationStatusRepository $notificationStatusRepository,
        Security $security,
        LoggerInterface $logger,
        EsputnikEmailClient $client
    )
    {
        parent::__construct($notificationStatusRepository, $security, $logger, $client);
    }
}