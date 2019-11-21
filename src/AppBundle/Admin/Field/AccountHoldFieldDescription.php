<?php

namespace AppBundle\Admin\Field;

use AppBundle\Service\AccountManager;
use Sonata\AdminBundle\Exception\NoValueException;
use AppBundle\Form\DataTransformer\MoneyTransformer;
use Sonata\DoctrineORMAdminBundle\Admin\FieldDescription;

class AccountHoldFieldDescription extends FieldDescription
{
    /**
     * @inheritdoc
     */
    protected $options = ['divisor' => 100];

    /**
     * @var AccountManager
     */
    private $accountManager;

    /**
     * @param AccountManager $accountManager
     * @param array          $options
     */
    public function __construct(AccountManager $accountManager, array $options = array())
    {
        parent::__construct();

        $this->accountManager = $accountManager;
        $this->options = array_merge_recursive($this->options, $options);
    }

    /**
     * @param $object
     * @param $fieldName
     *
     * @return float|int
     *
     * @throws NoValueException
     */
    public function getFieldValue($object, $fieldName)
    {
        $value = $this->accountManager->getHoldAmount(parent::getFieldValue($object, $fieldName));
        $divisor = (int)$this->getOption('divisor');

        $transformer = new MoneyTransformer($divisor);

        return $transformer->transform($value);
    }
}