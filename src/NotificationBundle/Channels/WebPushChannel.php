<?php

namespace NotificationBundle\Channels;

use NotificationBundle\Client\EsputnikWebPushClient;
use NotificationBundle\Repository\NotificationStatusRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

class WebPushChannel extends BaseChannel
{
    const NAME = 'WEB_PUSH_CHANNEL';

    public function __construct(
        NotificationStatusRepository $notificationStatusRepository,
        Security $security,
        LoggerInterface $logger,
        EsputnikWebPushClient $client
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
        if(!$data['push_token']){
            return;
        }

        if ($case && !$this->isAllowed($case, self::NAME)) {
            return;
        }

        try {
            $this->client->send($data);
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());
        }
    }
}