<?php

namespace AppBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class BitMaskToArrayTransformer implements DataTransformerInterface
{
    /**
     * @var int
     */
    private $maxValue;

    /**
     * @param int $maxValue
     */
    public function __construct(int $maxValue)
    {
        $this->maxValue = $maxValue;
    }

    /**
     * @inheritdoc
     */
    public function transform($value)
    {
        $newValue = [];

        if (!$value) {
            return $newValue;
        }

        for ($x = 1; $x <= $this->maxValue; $x *= 2) {
            if ($value & $x) {
                $newValue[] = $x;
            }
        }

        return $newValue;
    }

    /**
     * @inheritdoc
     */
    public function reverseTransform($value)
    {
        $newValue = null;

        if (!is_array($value)) {
            return $newValue;
        }

        $value = array_unique($value);

        for ($x = 1; $x <= $this->maxValue; $x *= 2) {
            if (in_array($x, $value)) {
                $newValue += $x;
            }
        }

        return $newValue;
    }
}