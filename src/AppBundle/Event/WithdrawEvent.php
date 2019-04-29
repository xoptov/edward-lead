<?php

namespace AppBundle\Event;

use AppBundle\Entity\Withdraw;
use Symfony\Component\EventDispatcher\Event;

class WithdrawEvent extends Event
{
    const CREATED = 'withdraw_created';

    /**
     * @var Withdraw
     */
    private $withdraw;

    /**
     * @param Withdraw $withdraw
     */
    public function __construct(Withdraw $withdraw)
    {
        $this->withdraw = $withdraw;
    }

    /**
     * @return Withdraw
     */
    public function getWithdraw(): Withdraw
    {
        return $this->withdraw;
    }
}