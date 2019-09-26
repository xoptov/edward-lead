<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueLead extends Constraint
{
    public $messageForExchange = 'Лид не уникальный в пределах биржи';

    public $messageForRoom = 'Лид не уникальный в пределах комнаты';

    /**
     * @inheritdoc
     */
    public function getTargets()
    {
        return Constraint::CLASS_CONSTRAINT;
    }
}