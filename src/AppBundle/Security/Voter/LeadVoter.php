<?php

namespace AppBundle\Security\Voter;

use AppBundle\Entity\Lead;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class LeadVoter extends Voter
{
    const VIEW = 'view';

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

        if (!in_array($attribute, [self::VIEW])) {
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
        if ($subject->isActive() && self::VIEW === $attribute) {
            return true;
        }

        if ($this->decisionManager->decide($token, [self::VIEW])) {
            return true;
        }

        if ($subject->getUser() === $token->getUser()) {
            return true;
        }

        if ($subject->getBuyer() === $token->getUser() && self::VIEW === $attribute) {
            return true;
        }

        return false;
    }
}