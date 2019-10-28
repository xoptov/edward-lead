<?php

namespace AppBundle\Service;

use AppBundle\Entity\City;
use AppBundle\Entity\Region;
use Doctrine\ORM\EntityManagerInterface;

class CitiesImporter
{
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param array $records
     *
     * @return array|null
     */
    public function import(array $records): ?array
    {
        $result = [
            'cityImported' => 0,
            'cityDuplicate' => 0,
            'regionImported' => 0,
            'regionDuplicate' => 0,
            'emptyRecords' => 0
        ];

        if (empty($records)) {
            return $result;
        }

        $records = $this->deleteDuplicates($records);

        $existedRegions = $this->entityManager
            ->getRepository(Region::class)
            ->findAll();

        $existedCities = $this->entityManager
            ->getRepository(City::class)
            ->findAll();

        foreach ($records as $record) {
            if (
                empty($record['city'])
                || empty($record['region'])
                || empty($record['timezone'])
            ) {
                continue;
            }
            /** @var City $existedCity */
            foreach ($existedCities as $existedCity) {
                if (0 === $this->compare($record['city'], $existedCity->getName())) {
                    $result['cityDuplicate']++;
                    continue;
                }

                $newCity = new City();
                $newCity
                    ->setName($record['city'])
                    ->setTimezone($record['timezone'])
                    ->setEnabled(true);
                $this->entityManager->persist($newCity);
                $result['cityImported']++;

                $foundRegion = null;

                foreach ($existedRegions as $existedRegion) {
                    if (0 === $this->compare($record['region'], $existedRegion->getName())) {
                        $foundRegion = $existedRegion;
                        $result['regionDuplicate']++;
                        break;
                    }
                }

                if (!$foundRegion) {
                    $newRegion = new Region();
                    $newRegion
                        ->setName($record['region'])
                        ->setEnabled(true);
                    $this->entityManager->persist($newRegion);

                    $newCity->setRegion($newRegion);

                    $existedRegions[] = $newRegion;
                    $result['regionImported']++;
                }
            }
        }

        $this->entityManager->flush();

        return $result;
    }

    /**
     * @param string $name1
     * @param string $name2
     *
     * @return int
     */
    private function compare(string $name1, string $name2): int
    {
        $name1 = $this->normalizeName($name1);
        $name2 = $this->normalizeName($name2);

        return substr_compare($name1, $name2, 0);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function normalizeName(string $name): string
    {
        return mb_strtolower(preg_replace('/[\-\/\\\s]/', '', $name));
    }

    /**
     * @param array $records
     *
     * @return array
     */
    private function deleteDuplicates(array &$records): array
    {
        $toDelete = [];

        for ($x = 0; $x < count($records) - 1; $x++) {
            for ($y = $x + 1; $y < count($records); $y++) {
                if (0 === $this->compare($records[$x]['city'], $records[$y]['city'])
                    && 0 === $this->compare($records[$x]['region'], $records[$y]['region'])
                ) {
                    $toDelete[] = $x;
                }
            }
        }

        $newRecords = [];

        foreach ($records as $key => $record) {
            if (in_array($key, $toDelete))
                continue;
            $newRecords[] = $record;
        }

        return $newRecords;
    }
}