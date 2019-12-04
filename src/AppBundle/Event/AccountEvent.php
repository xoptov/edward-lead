<?php

namespace AppBundle\Event;

use AppBundle\Entity\Account;
use AppBundle\Entity\ClientAccount;
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
     * @param ClientAccount $account
     */
    public function __construct(ClientAccount $account)
    {
        $this->account = $account;
    }

    /**
     * @return ClientAccount
     */
    public function getAccount(): ClientAccount
    {
        return $this->account;
    }
}
