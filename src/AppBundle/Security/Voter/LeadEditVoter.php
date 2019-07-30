<?php

namespace AppBundle\Security\Voter;

use AppBundle\Entity\Lead;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class LeadEditVoter extends LeadVoter
{
    const OPERATION = 'edit';

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
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        if ($subject->getUser() === $token->getUser()) {
            return true;
        }

        return false;
    }
}