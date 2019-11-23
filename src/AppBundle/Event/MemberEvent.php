<?php

namespace AppBundle\Event;

use AppBundle\Entity\Member;
use Symfony\Component\EventDispatcher\Event;

class MemberEvent extends Event
{
    const JOINED  = 'member.joined';
    const REMOVED = 'member.removed';

    /**
     * @var Member
     */
    private $member;

    /**
     * @param Member $member
     */
    public function __construct(Member $member)
    {
        $this->member = $member;
    }

    /**
     * @return Member
     */
    public function getMember(): Member
    {
        return $this->member;
    }
}