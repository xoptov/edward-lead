<?php

namespace AppBundle\Admin\Field;

use Sonata\AdminBundle\Exception\NoValueException;
use Sonata\DoctrineORMAdminBundle\Admin\FieldDescription;

class MoneyFieldDescription extends FieldDescription
{
    /**
     * @inheritdoc
     */
    protected $options = ['divisor' => 100];

    /**
     * @inheritdoc
     *
     * @throws NoValueException
     */
    public function getFieldValue($object, $fieldName)
    {
        $value = (int)parent::getFieldValue($object, $fieldName);
        $divisor = (int)$this->getOption('divisor');

        return $value / $divisor;
    }
}