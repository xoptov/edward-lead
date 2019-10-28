<?php

namespace AppBundle\Command;

use AppBundle\Entity\Lead;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ArchiveStaledLeadsCommand extends Command
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $ttl;

    /**
     * @param Connection  $connection
     * @param int         $ttl
     * @param null|string $name
     */
    public function __construct(Connection $connection, int $ttl, ?string $name = null)
    {
        parent::__construct($name);

        $this->connection = $connection;
        $this->ttl = $ttl;
    }

    protected function configure()
    {
        $this
            ->setName('app:lead:archive')
            ->setDescription('Команда для отправки лидов в архив если они со статусом expect и старше 48 часов');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sql = <<<SQL
UPDATE lead
SET status = :archive 
WHERE status = :expect 
  AND timer_end_at IS NULL
  AND UNIX_TIMESTAMP(NOW()) > (UNIX_TIMESTAMP(created_at) + :ttl)
SQL;

        try {
            $stmt = $this->connection->prepare($sql);
        } catch (DBALException $e) {
            $output->writeln($e->getMessage());
            return;
        }

        $result = $stmt->execute([
            'archive' => Lead::STATUS_ARCHIVE,
            'expect' => Lead::STATUS_EXPECT,
            'ttl' => $this->ttl * 3600
        ]);

        if (!$result) {
            $output->writeln($stmt->errorCode() . ': Выполнения SQL запроса на архивирования лидов');
            return;
        }

        $output->writeln('Лидов отправлено в архив: ' . $stmt->rowCount());
    }
}
