<?php

namespace NotificationBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use NotificationBundle\Model\AccountInterface;

class AccountEvent extends Event
{
    const BALANCE_APPROACHING_ZERO     = 'account.balance_approaching_zero';
    const BALANCE_LESS_THEN_LOWER      = 'account.balance_less_then_lower';
    const INSUFFICIENT_FUNDS_FOR_TRADE = 'account.insufficient_funds_for_trade';
    const INSUFFICIENT_FUNDS_FOR_CALL  = 'account.insufficiant_funds_for_call';
    
    /**
     * @var AccountInterface
     */
    private $account;

    /**
     * @param AccountInterface $account
     */
    public function __construct(AccountInterface $account)
    {
        $this->account = $account;
    }

    /**
     * @return AccountInterface
     */
    public function getAccount(): AccountInterface
    {
        return $this->account;
    } 
}
