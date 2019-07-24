<?php

namespace AppBundle\Form\Type\DataTransformer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EntityToIdTransformer implements DataTransformerInterface
{
    /** @var EntityManager */
    protected $em;

    /** @var string */
    protected $dc;

    /**
     * EntityToIdTransformer constructor.
     * @param EntityManager $entityManager
     * @param string $dataClass
     */
    public function __construct(EntityManager $entityManager, $dataClass)
    {
        $this->em = $entityManager;

        $this->dc = $dataClass;
    }

    /**
     * @param mixed $value The value in the original representation
     *
     * @return mixed The value in the transformed representation
     *
     * @throws TransformationFailedException when the transformation fails
     */
    public function transform($value)
    {
        if ($value instanceof $this->dc && method_exists($value, "getId")) {
            return $value->getId();
        }

        return null;
    }

    /**
     * @param mixed $value The value in the transformed representation
     *
     * @return mixed The value in the original representation
     *
     * @throws TransformationFailedException when the transformation fails
     */
    public function reverseTransform($value)
    {
        if (intval($value)) {
            return $this->em->getRepository($this->dc)->find($value);
        }

        return null;
    }
}