<?php

namespace AppBundle\Form\DataTransformer;

use AppBundle\Util\Formatter;
use Symfony\Component\Form\DataTransformerInterface;

class MoneyTransformer implements DataTransformerInterface
{
    /**
     * @param mixed $value
     * 
     * @return float
     */
    public function transform($value)
    {
        if ($value) {
            return floatval($value / Formatter::MONEY_DIVISOR);
        }

        return 0.0;
    }

    /**
     * @param mixed $value
     * 
     * @return int
     */
    public function reverseTransform($value)
    {
        if ($value) {
            return intval($value * Formatter::MONEY_DIVISOR);
        }

        return 0;
    }
}