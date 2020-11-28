<?php

namespace AppBundle\Security\Voter;

use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserVoter extends Voter
{
    const EDIT   = 'edit';
    const VIEW   = 'view';

    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::EDIT, self::VIEW])) {
            return false;
        }

        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($token->getUser() === $subject) {
            return true;
        }

        return false;
    }
}