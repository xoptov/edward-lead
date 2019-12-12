<?php

namespace AppBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\IdentifiableInterface;
use Symfony\Component\Form\DataTransformerInterface;

class EntityToIdTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private $dataClass;

    /**
     * @param EntityManagerInterface $entityManager
     * @param string                 $dataClass
     */
    public function __construct(EntityManagerInterface $entityManager, string $dataClass)
    {
        $this->entityManager = $entityManager;
        $this->dataClass = $dataClass;
    }

    /**
     * @param IdentifiableInterface $value
     *
     * @return mixed
     */
    public function transform($value)
    {
        if ($value instanceof IdentifiableInterface) {
            return $value->getId();
        } elseif (is_array($value)) {
            return array_map(function(IdentifiableInterface $item) {
                return $item->getId();
            }, $value);

        }

        return null;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function reverseTransform($value)
    {
        if (!$value) {
            return null;
        }

        if (is_array($value)) {
            $result = $this->entityManager->getRepository($this->dataClass)
                ->findBy(['id' => $value]);
        } else {
            $result = $this->entityManager->getRepository($this->dataClass)
                ->find($value);
        }

        return $result;
    }
}