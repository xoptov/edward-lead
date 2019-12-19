<?php

namespace AppBundle\Command;

use AppBundle\Entity\Lead;
use AppBundle\Event\LeadEvent;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class LeadInWorkTooLongNotificationCommand extends Command
{
    const STATUS_SUCCESS = 0;
    const STATUS_ERROR = 1;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param EntityManagerInterface   $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface          $logger
     * @param null|string              $name
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger,
        ?string $name = null
    ) {
        parent::__construct($name);

        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('app:notification:in_work_too_long')
            ->setDescription('Комнада для нотификации о там что лид находится в работе слишком долго');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $leads = $this->entityManager->getRepository(Lead::class)
                ->getInWorkTooLong();
        } catch (DBALException $e) {
            $this->logger->error(
                'Ошибка подготовки запроса к БД',
                ['message' => $e->getMessage()]
            );
            return self::STATUS_ERROR;
        }

        foreach ($leads as $lead) {
            try {
                $this->eventDispatcher->dispatch(
                    LeadEvent::IN_WORK_TOO_LONG,
                    new LeadEvent($lead)
                );
            } catch (\Exception $e) {
                $this->logger->error('Ошибка нотификации', ['message' => $e->getMessage()]);
                continue;
            }
        }

        return self::STATUS_SUCCESS;
    }
}