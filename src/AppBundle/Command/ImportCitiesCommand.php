<?php

namespace AppBundle\Command;

use League\Csv\Reader;
use AppBundle\Importer\CitiesImporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCitiesCommand extends Command
{
    const RESULT_OK = 0;
    const RESULT_ERROR = 1;

    /**
     * @var CitiesImporter
     */
    private $importer;

    /**
     * @param CitiesImporter $importer
     * @param null|string    $name
     */
    public function __construct(CitiesImporter $importer, ?string $name = null)
    {
        parent::__construct($name);

        $this->importer = $importer;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('app:city:import')
            ->setDescription('Команда для импорта городов в БД')
            ->addArgument('path', InputArgument::OPTIONAL, 'Путь к CSV файлу с данными', 'cities.csv');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $reader = Reader::createFromPath($input->getArgument('path'));

        $records = [];

        foreach ($reader->getRecords(['city', 'region', 'timezone']) as $record) {
            $records[] = $record;
        }

        $this->importer->import($records);

        $output->writeln('Import data about cities complete.');
        $output->writeln([
            'Cities imported: ' . $this->importer->getImportedCities(),
            'Cities duplicate skipped: ' . $this->importer->getCityDuplicates(),
            'Region imported: ' . $this->importer->getImportedRegions(),
            'Region duplicate skipped: ' . $this->importer->getRegionDuplicates(),
            'Empty skipped: ' . $this->importer->getEmptyRecords(),
            'Validation violations: ' . count($this->importer->getViolations())
        ]);
    }
}