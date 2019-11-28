<?php

namespace AppBundle\Event;

use AppBundle\Entity\Account;
use Symfony\Component\EventDispatcher\Event;

class AccountEvent extends Event
{
    const BALANCE_APPROACHING_ZERO   = 'account.balance_approaching_zero';
    const BALANCE_LOWER_THEN_MINIMAL = 'account.balance_lower_then_minimal';

    /**
     * @var Account
     */
    private $account;

    /**
     * @param Account $account
     */
    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    /**
     * @return Account
     */
    public function getAccount(): Account
    {
        return $this->account;
    }
}
