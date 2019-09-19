<?php

namespace AppBundle\Command;

use AppBundle\Entity\Account;
use AppBundle\Entity\Trade;
use AppBundle\Service\TradeManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnexpectedResultException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AutoFinishTradesCommand extends Command
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
     * @var int
     */
    private $staleAfterHours;

    /**
     * @param EntityManagerInterface $entityManager
     * @param TradeManager           $tradeManager
     * @param int                    $staleAfterHours
     * @param null|string            $name
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TradeManager $tradeManager,
        int $staleAfterHours,
        ?string $name = null
    ) {
        parent::__construct($name);

        $this->entityManager = $entityManager;
        $this->tradeManager = $tradeManager;
        $this->staleAfterHours = $staleAfterHours;
    }

    protected function configure()
    {
        $this
            ->setName('app:trade:completing')
            ->setDescription('Команда для завершения подвисших сделок')
            ->addArgument('stale_after', InputArgument::REQUIRED, 'Количество часов после которой заявка считается экспирированной', $this->staleAfterHours);
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $trades = $this->entityManager->getRepository(Trade::class)
            ->getWithWarrantyAndIncomplete();

        if (empty($trades)) {
            $output->writeln('Нет подвисших сделок');
            return;
        }

        try {
            $feesAccount = $this->entityManager->getRepository(Account::class)
                ->getFeesAccount();
        } catch (UnexpectedResultException $e) {
            $output->writeln($e->getMessage());
            return;
        }

        $staleTimeBound = new \DateTime('-'.$input->getArgument('stale_after').' hours');

        /** @var Trade $trade */
        foreach ($trades as $trade) {
            $this->tradeManager->autoFinish($trade, $feesAccount, $staleTimeBound);
        }

        $output->writeln('Обработка подвисших сделок завершена');
    }
}