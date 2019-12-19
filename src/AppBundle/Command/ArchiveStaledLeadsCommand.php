<?php

namespace AppBundle\Command;

use AppBundle\Entity\Lead;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ArchiveStaledLeadsCommand extends Command
{
    const STATUS_SUCCESS = 0;
    const STATUS_ERROR = 1;

    const HOURS = 48;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection  $connection
     * @param null|string $name
     */
    public function __construct(Connection $connection, ?string $name = null)
    {
        parent::__construct($name);

        $this->connection = $connection;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('app:lead:archive')
            ->setDescription('Команда для отправки лидов в архив если они со статусом expect и старше '.self::HOURS.' часов')
            ->addArgument(
                'hours',
                InputArgument::OPTIONAL,
                'Кол-во часов после которых отправлять в архив',
                self::HOURS
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sql = <<<SQL
UPDATE lead
SET status = :target_status, updated_at = NOW()
WHERE status = :result_status
  AND timer_end_at IS NULL
  AND DATE_ADD(created_at, INTERVAL :hours HOUR) < NOW()
SQL;

        $hours = $input->getArgument('hours');

        try {
            $stmt = $this->connection->prepare($sql);
        } catch (DBALException $e) {
            $output->writeln($e->getMessage());
            return self::STATUS_ERROR;
        }

        $result = $stmt->execute([
            'target_status' => Lead::STATUS_ARCHIVE,
            'result_status' => Lead::STATUS_EXPECT,
            'hours' => $hours
        ]);

        if (!$result) {
            $output->writeln($stmt->errorCode() . ': Выполнения SQL запроса на архивирования лидов');
            return self::STATUS_ERROR;
        }

        $output->writeln('Лидов отправлено в архив: ' . $stmt->rowCount());

        return self::STATUS_SUCCESS;
    }
}
