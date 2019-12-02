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
     * @var int
     */
    private $bound;

    /**
     * @param Account  $account
     * @param int|null $bound
     */
    public function __construct(Account $account, ?int $bound = null)
    {
        $this->account = $account;
        $this->bound = $bound;
    }

    /**
     * @return Account
     */
    public function getAccount(): Account
    {
        return $this->account;
    }

    /**
     * @return int|null
     */
    public function getBound(): ?int
    {
        return $this->bound;
    }
}
