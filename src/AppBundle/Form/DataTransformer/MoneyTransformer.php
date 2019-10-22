<?php

namespace AppBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class MoneyTransformer implements DataTransformerInterface
{
    /**
     * @var int
     */
    private $divisor;

    /**
     * @param int $divisor
     */
    public function __construct(?int $divisor = 100)
    {
        $this->divisor = $divisor;
    }

    /**
     * @inheritdoc
     */
    public function transform($value)
    {
        if ($value) {
            return (float)$value / $this->divisor;
        }

        return 0;
    }

    /**
     * @inheritdoc
     */
    public function reverseTransform($value)
    {
        if ($value) {
            return $this->divisor * $value;
        }

        return 0;
    }
}