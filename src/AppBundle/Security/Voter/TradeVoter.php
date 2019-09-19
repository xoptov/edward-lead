<?php

namespace AppBundle\Security\Voter;

use AppBundle\Entity\Trade;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TradeVoter extends Voter
{
    const ACCEPT    = 'accept';
    const REJECT    = 'reject';
    const MAKE_CALL = 'make_call';

    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        if (!$subject instanceof Trade) {
            return false;
        }

        if (!in_array($attribute, [self::ACCEPT, self::REJECT, self::MAKE_CALL])) {
            return false;
        }

        return true;
    }

    /**
     * @param string         $attribute
     * @param Trade          $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($subject->getBuyer() === $token->getUser()) {
            return true;
        }

        return false;
    }
}