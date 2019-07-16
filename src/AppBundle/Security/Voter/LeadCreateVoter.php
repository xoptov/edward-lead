<?php

namespace AppBundle\Security\Voter;

use AppBundle\Entity\Lead;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class LeadCreateVoter extends LeadVoter
{
    const OPERATION = 'create';

    /**
     * @inheritdoc
     */
    protected function getOperation(): string
    {
        return self::OPERATION;
    }

    /**
     * @param string         $attribute
     * @param Lead           $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        if (!$subject->getRoom()) {
            return true;
        }

        if ($this->roomManager->isMember($subject->getRoom(), $token->getUser())) {
            return true;
        }

        return false;
    }
}