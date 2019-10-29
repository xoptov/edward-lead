<?php

namespace AppBundle\Importer;

use AppBundle\Entity\City;
use AppBundle\Entity\Region;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CitiesImporter
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var int
     */
    private $importedCities = 0;

    /**
     * @var int
     */
    private $importedRegions = 0;

    /**
     * @var int
     */
    private $cityDuplicates = 0;

    /**
     * @var int
     */
    private $regionDuplicates = 0;

    /**
     * @var int
     */
    private $emptyRecords = 0;

    /**
     * @var ConstraintViolation[]
     */
    private $violations = [];

    /**
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface     $validator
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * @param array $records
     *
     * @return bool
     */
    public function import(array &$records): bool
    {
        $this->importedCities   = 0;
        $this->importedRegions  = 0;
        $this->cityDuplicates   = 0;
        $this->regionDuplicates = 0;
        $this->emptyRecords     = 0;
        $this->violations       = [];

        if (empty($records)) {
            return false;
        }

        $data = $this->deleteDuplicates($records);

        $existedRegions = $this->entityManager
            ->getRepository(Region::class)
            ->findAll();

        $existedCities = $this->entityManager
            ->getRepository(City::class)
            ->findAll();

        foreach ($data as $item) {
            if (
                empty($item['city'])
                || empty($item['region'])
                || empty($item['timezone'])
            ) {
                $this->emptyRecords++;
                continue;
            }

            $foundedCity = false;

            /** @var City $existedCity */
            foreach ($existedCities as $existedCity) {
                if (0 === $this->compare($item['city'], $existedCity->getName())
                    && 0 === $this->compare($item['region'], $existedCity->getRegionName())
                ) {
                    $foundedCity = true;
                    $this->cityDuplicates++;
                    break;
                }
            }

            if ($foundedCity) {
                continue;
            }

            $city = $this->createCity($item['city'], $item['timezone']);
            $existedCities[] = $city;

            $foundedRegion = false;

            $region = null;

            foreach ($existedRegions as $existedRegion) {
                if (0 === $this->compare($item['region'], $existedRegion->getName())) {
                    $foundedRegion = true;
                    $this->regionDuplicates++;
                    $region = $existedRegion;
                    break;
                }
            }

            if (!$foundedRegion) {
                $region = $this->createRegion($item['region']);
                $existedRegions[] = $region;
            }

            $city->setRegion($region);

            $violations = $this->validator->validate($city);

            if ($violations->count()) {
                foreach ($violations as $violation) {
                    $this->violations[] = $violation;
                }
            } else {
                $this->entityManager->persist($city);
                $this->entityManager->persist($region);

                $this->importedCities++;
                if (!$foundedRegion) {
                    $this->importedRegions++;
                }
            }
        }

        $this->entityManager->flush();

        return true;
    }

    /**
     * @return int
     */
    public function getImportedCities(): int
    {
        return $this->importedCities;
    }

    /**
     * @return int
     */
    public function getImportedRegions(): int
    {
        return $this->importedRegions;
    }

    /**
     * @return int
     */
    public function getCityDuplicates(): int
    {
        return $this->cityDuplicates;
    }

    /**
     * @return int
     */
    public function getRegionDuplicates(): int
    {
        return $this->regionDuplicates;
    }

    /**
     * @return int
     */
    public function getEmptyRecords(): int
    {
        return $this->emptyRecords;
    }

    /**
     * @return ConstraintViolation[]
     */
    public function getViolations(): array
    {
        return $this->violations;
    }

    /**
     * @param string $name
     * @param string $timezone
     *
     * @return City
     */
    private function createCity(string $name, string $timezone): City
    {
        $city = new City();
        $city->setName($name)
            ->setTimezone($timezone)
            ->setEnabled(true);

        return $city;
    }

    /**
     * @param string $name
     *
     * @return Region
     */
    private function createRegion(string $name): Region
    {
        $region = new Region();
        $region->setName($name)
            ->setEnabled(true);

        return $region;
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