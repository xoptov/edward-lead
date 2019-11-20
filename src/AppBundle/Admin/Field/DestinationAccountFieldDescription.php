<?php

namespace AppBundle\Admin\Field;

use AppBundle\Entity\Account;
use AppBundle\Entity\Operation;
use AppBundle\Entity\MonetaryTransaction;
use Sonata\DoctrineORMAdminBundle\Admin\FieldDescription;

class DestinationAccountFieldDescription extends FieldDescription
{
    /**
     * @inheritdoc
     */
    public function getFieldValue($object, $fieldName)
    {
        if (!$object instanceof Operation) {
            return null;
        }

        $incomeTransactions = $object->getIncomeTransactions();
        $incomeTransaction = reset($incomeTransactions);

        /** @var MonetaryTransaction */
        if ($incomeTransaction) {
            /** @var Account $destinationAccount */
            $destinationAccount = $incomeTransaction->getAccount();

            return $destinationAccount->getDescription();
        }

        return null;
    }
}