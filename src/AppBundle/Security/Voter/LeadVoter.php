<?php

namespace AppBundle\Security\Voter;

use AppBundle\Entity\Lead;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class LeadVoter extends Voter
{
    const VIEW       = 'view';
    const EDIT       = 'edit';
    const FIRST_CALL = 'first_call';

    /**
     * @var AccessDecisionManagerInterface
     */
    private $decisionManager;

    /**
     * @param AccessDecisionManagerInterface $decisionManager
     */
    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    /**
     * @param string $attribute
     * @param mixed  $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        if (!$subject instanceof Lead) {
            return false;
        }

        if (!in_array($attribute, [self::VIEW, self::EDIT, self::FIRST_CALL])) {
            return false;
        }

        return true;
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

        if (self::VIEW === $attribute) {
            if ($subject->isActive()) {
                return true;
            }

            if ($subject->getUser() === $token->getUser()) {
                return true;
            }

            if ($subject->hasTrade() && $subject->getBuyer() === $token->getUser()) {
                return true;
            }
        }

        if (self::FIRST_CALL === $attribute) {
            if ($subject->isReserved() && $subject->getBuyer() === $token->getUser()) {
                return true;
            }
        }

        if (self::EDIT === $attribute) {
            if ($subject->getUser() === $token->getUser()) {
                return true;
            }
        }

        return false;
    }
}