<?php

namespace AppBundle\Security\Voter;

use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use AppBundle\Service\RoomManager;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class RoomVoter extends Voter
{
    const VIEW          = 'view';
    const REVOKE_MEMBER = 'revoke_member';
    const DEACTIVATE    = 'deactivate';

    /**
     * @var RoomManager
     */
    private $roomManager;

    /**
     * @param RoomManager $roomManager
     */
    public function __construct(RoomManager $roomManager)
    {
        $this->roomManager = $roomManager;
    }

    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        if (!$subject instanceof Room) {
            return false;
        }

        if (!in_array($attribute, [self::VIEW, self::REVOKE_MEMBER, self::DEACTIVATE])) {
            return false;
        }

        return true;
    }

    /**
     * @param string         $attribute
     * @param Room           $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        if ($subject->isOwner($user)) {
            return true;
        }

        if (self::VIEW === $attribute && $this->roomManager->isMember($subject, $user)) {
            return true;
        }

        return false;
    }
}