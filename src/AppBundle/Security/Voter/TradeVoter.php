<?php

namespace AppBundle\Security\Voter;

use AppBundle\Entity\Trade;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class TradeVoter extends Voter
{
    const SUCCESS = 'success';
    const REJECT = 'reject';

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
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        if (!$subject instanceof Trade) {
            return false;
        }

        if (!in_array($attribute, [self::SUCCESS, self::REJECT])) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($this->decisionManager->decide($token, ['ROLE_ADMIN'])) {
            return true;
        }

        if ($subject->getBuyer() === $token->getUser()) {
            return true;
        }

        return false;
    }
}