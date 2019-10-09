<?php

namespace AppBundle\Security\Voter;

use AppBundle\Entity\User;
use AppBundle\Entity\PhoneCall;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class PhoneCallVoter extends Voter
{
    const LISTEN = 'listen';

    /**
     * @param string $attribute
     * @param mixed  $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        if (!$subject instanceof PhoneCall) {
            return false;
        }

        if (self::LISTEN !== $attribute) {
            return false;
        }

        return true;
    }

    /**
     * @param string         $attribute
     * @param PhoneCall      $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();
        $trade = $subject->getTrade();

        if ($trade && $trade->isDealMember($user)) {
            return true;
        }

        return false;
    }
}