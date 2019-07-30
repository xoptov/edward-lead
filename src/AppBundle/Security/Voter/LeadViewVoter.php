<?php

namespace AppBundle\Security\Voter;

use AppBundle\Entity\Lead;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class LeadViewVoter extends LeadVoter
{
    const OPERATION = 'view';

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
        if ($this->decisionManager->decide($token, ['ROLE_ADMIN'])) {
            return true;
        }

        // Если пользователь создатель лида то он в любом случае может просматривать информацию лида.
        if ($subject->getUser() === $token->getUser()) {
            return true;
        }

        if ($subject->hasTrade()) {
            $trade = $subject->getTrade();
            if ($trade->getBuyer() === $token->getUser()) {
                return true;
            }
        }

        if ($subject->getRoom()) {

            /** @var User $user */
            $user = $token->getUser();

            // Если пользователь состоит в комнате и лид активен тогда пользователю можно получать информацию о лиде.
            if ($this->roomManager->isMember($subject->getRoom(), $user) && $subject->isActive()) {
                return true;
            }

        } elseif ($subject->isActive()) {
            return true;
        }

        return false;
    }
}