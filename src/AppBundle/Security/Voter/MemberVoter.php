<?php

namespace AppBundle\Security\Voter;

use AppBundle\Entity\User;
use AppBundle\Entity\Member;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class MemberVoter extends Voter
{
    const DELETE = 'delete';

    /**
     * @param string $attribute
     * @param mixed  $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        if (!$subject instanceof Member) {
            return false;
        }

        if (self::DELETE !== $attribute) {
            return false;
        }

        return true;
    }

    /**
     * @param string         $attribute
     * @param Member         $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        $memberUser = $subject->getUser();
        $room = $subject->getRoom();

        if (self::DELETE) {
            if ($room->isOwner($user) && $memberUser !== $user) {
                return true;
            }
            if (!$room->isOwner($user) && $memberUser === $user) {
                return true;
            }
        }

        return false;
    }
}