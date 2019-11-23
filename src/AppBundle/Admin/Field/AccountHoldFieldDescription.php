<?php

namespace AppBundle\Admin\Field;

use AppBundle\Service\AccountManager;
use Sonata\AdminBundle\Exception\NoValueException;
use AppBundle\Form\DataTransformer\MoneyTransformer;
use Sonata\DoctrineORMAdminBundle\Admin\FieldDescription;

class AccountHoldFieldDescription extends FieldDescription
{
    /**
     * @var AccountManager
     */
    private $accountManager;

    /**
     * @param AccountManager $accountManager
     */
    public function __construct(AccountManager $accountManager)
    {
        parent::__construct();

        $this->accountManager = $accountManager;
    }

    /**
     * @param mixed  $object
     * @param string $fieldName
     *
     * @return float
     *
     * @throws NoValueException
     */
    public function getFieldValue($object, $fieldName)
    {
        $value = $this->accountManager->getHoldAmount(parent::getFieldValue($object, $fieldName));
        $transformer = new MoneyTransformer();

        return $transformer->transform($value);
    }
}