<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class RoomTimer extends Constraint
{
    /**
     * @inheritdoc
     */
    public function getTargets()
    {
        return Constraint::CLASS_CONSTRAINT;
    }
}