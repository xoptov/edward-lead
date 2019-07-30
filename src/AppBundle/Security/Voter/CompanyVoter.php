<?php

namespace AppBundle\Security\Voter;

use AppBundle\Entity\Company;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CompanyVoter extends Voter
{
    const EDIT = 'edit';

    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        if (self::EDIT !== $attribute) {
            return false;
        }

        if (!$subject instanceof Company) {
            return false;
        }

        return true;
    }

    /**
     * @param string         $attribute
     * @param Company        $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($subject->getUser() === $token->getUser()) {
            return true;
        }

        return false;
    }
}