<?php

namespace AppBundle\Security\Voter;

use AppBundle\Entity\Lead;
use AppBundle\Service\RoomManager;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

abstract class LeadVoter extends Voter
{
    /**
     * @var AccessDecisionManagerInterface
     */
    protected $decisionManager;

    /**
     * @var RoomManager
     */
    protected $roomManager;

    /**
     * @param AccessDecisionManagerInterface $decisionManager
     * @param RoomManager                    $roomManager
     */
    public function __construct(
        AccessDecisionManagerInterface $decisionManager,
        RoomManager $roomManager
    ) {
        $this->decisionManager = $decisionManager;
        $this->roomManager = $roomManager;
    }

    /**
     * @return string
     */
    abstract protected function getOperation(): string;

    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        if ($subject instanceof Lead && $this->getOperation() === $attribute) {
            return true;
        }

        return false;
    }
}