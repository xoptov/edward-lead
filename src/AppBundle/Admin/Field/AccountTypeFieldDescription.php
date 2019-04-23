<?php

namespace AppBundle\Admin\Field;

use AppBundle\Entity\Account;
use AppBundle\Entity\ClientAccount;
use AppBundle\Entity\IncomeAccount;
use AppBundle\Entity\OutgoingAccount;
use Sonata\DoctrineORMAdminBundle\Admin\FieldDescription;

class AccountTypeFieldDescription extends FieldDescription
{
    private $types = [
        Account::class => 'system',
        OutgoingAccount::class => 'outgoing',
        IncomeAccount::class => 'income',
        ClientAccount::class => 'client'
    ];

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function getFieldValue($object, $fieldName)
    {
        if (!isset($this->types[get_class($object)])) {
            throw new \Exception('Указан неподдерживаемый тип счёта');
        }

        return $this->types[get_class($object)];
    }
}