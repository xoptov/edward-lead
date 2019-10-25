<?php

namespace AppBundle\Command;

use AppBundle\Entity\Lead;
use AppBundle\Entity\Member;
use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use AppBundle\Service\TimerManager;
use AppBundle\Service\TradeManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TimerProcessCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TradeManager
     */
    private $tradeManager;

    /**
     * @var TimerManager
     */
    private $timerManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @param TradeManager           $tradeManager
     * @param TimerManager           $timerManager
     * @param null|string            $name
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TradeManager $tradeManager,
        TimerManager $timerManager,
        ?string $name = null
    ) {
        parent::__construct($name);

        $this->entityManager = $entityManager;
        $this->tradeManager = $tradeManager;
        $this->timerManager = $timerManager;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('app:timer:process')
            ->setDescription('Команда для обработки законченных таймеров у лидов');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $now = $this->timerManager->createDateTime();

        $leads = $this->entityManager->getRepository(Lead::class)
            ->getByEndedTimerAndExpect($now);

        if (empty($leads)) {
            $output->writeln('No leads with expired timer');

            return false;
        }

        $rooms = $this->entityManager->getRepository(Room::class)
            ->getByLeads($leads);

        if (empty($rooms)) {
            $output->writeln('Some thing goes wrong with rooms');
        }

        $members = $this->entityManager->getRepository(Member::class)
            ->getByRooms($rooms);

        if (empty($members)) {
            $output->writeln('Some thing goes wrong with members');
        }

        $users = $this->entityManager->getRepository(User::class)
            ->getByMembers($members);

        return false;
    }
}