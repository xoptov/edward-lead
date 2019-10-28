<?php

namespace AppBundle\Command;

use AppBundle\Entity\Lead;
use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use AppBundle\Entity\Account;
use AppBundle\Service\TimerManager;
use AppBundle\Service\TradeManager;
use AppBundle\Exception\TradeException;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Exception\OperationException;
use AppBundle\Exception\FinancialException;
use Doctrine\ORM\UnexpectedResultException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TimerProcessCommand extends Command
{
    const RESULT_OK = 0;
    const RESULT_ERROR = 1;

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

        if (empty($leads))
            return self::RESULT_OK;

        try {
            $feesAccount = $this->entityManager->getRepository(Account::class)
                ->getFeesAccount();
        } catch (UnexpectedResultException $e) {
            $output->writeln($e->getMessage());
            return self::RESULT_ERROR;
        }

        // Для гидрации прокси объектов комнат.
        $this->entityManager->getRepository(Room::class)
            ->getByLeads($leads);

        foreach ($leads as $lead) {

            $room = $lead->getRoom();

            if (empty($room))
                continue;

            $advertisers = $this->entityManager->getRepository(User::class)
                ->getAdvertisersInRoom($room);

            if (empty($advertisers))
                continue;

            $processedAdvertisers = []; // Для хранения уже обработанных рекламодателей.
            $tradeCreatedAndAccepted = false; // Флаг для отметки успешного создания и завершения сделки.

            do {
                $advertiser = $advertisers[rand(0, count($advertisers) - 1)];

                if (in_array($advertiser, $processedAdvertisers))
                    continue;

                $processedAdvertisers[] = $advertiser;

                try {
                    $trade = $this->tradeManager->start($advertiser, $lead->getUser(), $lead, false);

                    // Сразу же пробуем акцептировать сделку автоматически.
                    $this->tradeManager->accept($trade, $feesAccount);

                    if ($trade->isAccepted())
                        $tradeCreatedAndAccepted = true;

                } catch (FinancialException | TradeException | OperationException $e) {
                    $output->writeln($e->getMessage());
                }

            } while (!$tradeCreatedAndAccepted || count($processedAdvertisers) < count($advertisers));

            if (!$tradeCreatedAndAccepted) {
                $lead->setStatus(Lead::STATUS_ARCHIVE);
            }
        }

        $this->entityManager->flush();

        return self::RESULT_OK;
    }
}