<?php

namespace AppBundle\Security\Voter;

use AppBundle\Entity\Office;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class OfficeVoter extends Voter
{
    const VIEW   = 'view';
    const EDIT   = 'edit';
    const DELETE = 'delete';

    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject): bool
    {
        if ($subject instanceof Office
            && in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
        ) {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        /** @var Office $subject */
        if ($subject->getUser() === $token->getUser()) {
            return true;
        }

        return false;
    }
}