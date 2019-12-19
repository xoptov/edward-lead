<?php

namespace AppBundle\Command;

use AppBundle\Entity\Member;
use AppBundle\Event\MemberEvent;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RoomLastVisitNotificationCommand extends Command
{
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
            ->setName('app:notification:last_room_visit')
            ->setDescription('Команда для нотификации пользователей о последнем визите комнат')
            ->addArgument('days', InputArgument::REQUIRED, 'Количество дней');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $days = $input->getArgument('days');

        try {
            $members = $this->entityManager->getRepository(Member::class)
                ->getByRecentRoomVisits($days);

            foreach ($members as $member) {
                $this->eventDispatcher->dispatch(
                    MemberEvent::NO_VISIT_TOO_LONG,
                    new MemberEvent($member)
                );
            }

        } catch (DBALException $e) {
            $this->logger->error(
                'Произошла ошибка оповещения участников комнат которые не посещали комнаты некоторое количество дней',
                [
                    'days' => $days,
                    'message' => $e->getMessage()
                ]
            );
            $output->writeln('Произошла ошибка оповещения участников комнат');
        }
    }
}