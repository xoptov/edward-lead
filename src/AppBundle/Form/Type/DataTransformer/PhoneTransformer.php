<?php

namespace AppBundle\Form\Type\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class PhoneTransformer implements DataTransformerInterface
{
    /**
     * @inheritdoc
     */
    public function transform($value)
    {
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function reverseTransform($value)
    {
        if (!$value) {
            return null;
        }

        return preg_replace(['/\(|\)|\-|\s/', '/^8/', '/^\+/'], ['', '7', ''], $value);
    }
}