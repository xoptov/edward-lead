<?php

namespace AppBundle\Security\Voter;

use AppBundle\Entity\Company;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CompanyVoter extends Voter
{
    const EDIT = 'edit';
    const VIEW = 'view';

    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        if (!$subject instanceof Company) {
            return false;
        }

        if (!in_array($attribute, [self::EDIT, self::VIEW])) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var Company $subject */
        if ($subject->getUser() === $token->getUser()) {
            return true;
        }

        return false;
    }
}