<?php

namespace NotificationBundle\Event;

use NotificationBundle\Model\MemberInterface;

class MemberEvent extends Event
{
    const JOINED  = 'member.joined';
    const REMOVED = 'member.removed';

    /**
     * @param MemberInterface
     */
    private $member;

    /**
     * @param MemberInterface $member
     */
    public function __construct(MemberInterface $member)
    {
        $this->member = $member;
    }

    /**
     * @return MemberInterface
     */
    public function getMember(): MemberInterface
    {
        return $this->member;
    }
}
