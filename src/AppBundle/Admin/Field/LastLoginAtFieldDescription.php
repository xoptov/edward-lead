<?php

namespace AppBundle\Admin\Field;

use AppBundle\Entity\User;
use AppBundle\Entity\HistoryAction;
use Sonata\DoctrineORMAdminBundle\Admin\FieldDescription;

class LastLoginAtFieldDescription extends FieldDescription
{
    /**
     * @inheritdoc
     */
    public function getFieldValue($object, $fieldName)
    {
        if (!$object instanceof User) {
            return null;
        }

        $historyActions = $object->getHistoryActions();

        $loginActions = $historyActions->filter(function (HistoryAction $historyAction) {
            return $historyAction->getAction() === HistoryAction::ACTION_LOGIN;
        });

        $lastLoginAction = $loginActions->last();

        /** @var HistoryAction $lastLoginAction */
        if ($lastLoginAction) {
            return $lastLoginAction->getHappenedAt();
        }

        return null;
    }
}