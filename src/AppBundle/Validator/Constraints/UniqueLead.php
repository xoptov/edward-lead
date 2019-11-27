<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueLead extends Constraint
{
    /**
     * @var string
     */
    public $messageForExchange = 'Лид не уникальный в пределах биржи';

    /**
     * @var string
     */
    public $messageForRoom = 'Лид не уникальный в пределах комнаты';

    /**
     * @var int
     */
    public $tradePeriod;

    /**
     * @inheritdoc
     */
    public function getTargets()
    {
        return Constraint::CLASS_CONSTRAINT;
    }
}