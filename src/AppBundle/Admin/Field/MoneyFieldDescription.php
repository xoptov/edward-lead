<?php

namespace AppBundle\Admin\Field;

use Sonata\AdminBundle\Exception\NoValueException;
use AppBundle\Form\DataTransformer\MoneyTransformer;
use Sonata\DoctrineORMAdminBundle\Admin\FieldDescription;

class MoneyFieldDescription extends FieldDescription
{
    /**
     * @inheritdoc
     */
    protected $options = ['divisor' => 100];

    /**
     * @param array|null $options
     */
    public function __construct(?array $options = array())
    {
        parent::__construct();
        $this->options = array_merge_recursive($this->options, $options);
    }

    /**
     * @inheritdoc
     *
     * @throws NoValueException
     */
    public function getFieldValue($object, $fieldName)
    {
        $value = (int)parent::getFieldValue($object, $fieldName);
        $divisor = (int)$this->getOption('divisor');

        $transformer = new MoneyTransformer($divisor);

        return $transformer->transform($value);
    }
}