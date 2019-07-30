<?php

namespace AppBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class NumberToBooleanTransformer implements DataTransformerInterface
{
    const TYPE_YES = 1;
    const TYPE_NO  = 2;

    /**
     * @inheritdoc
     */
    public function transform($value)
    {
        if (true === $value) {
            return self::TYPE_YES;
        } elseif (false === $value) {
            return self::TYPE_NO;
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function reverseTransform($value)
    {
        if (self::TYPE_YES === (int)$value) {
            return true;
        } elseif (self::TYPE_NO === (int)$value) {
            return false;
        }

        return null;
    }
}