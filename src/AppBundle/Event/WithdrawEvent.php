<?php

namespace AppBundle\Event;

use AppBundle\Entity\Withdraw;
use Symfony\Component\EventDispatcher\Event;

class WithdrawEvent extends Event
{
    const NEW_CREATED = 'withdraw.new_created';
    const ACCEPTED    = 'withdraw.accepted';

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