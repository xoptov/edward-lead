<?php

namespace AppBundle\Exception;

use Throwable;
use AppBundle\Entity\Account;

class AccountException extends FinancialException
{
    /**
     * @var Account
     */
    private $account;

    /**
     * @param Account        $account
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(Account $account, string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

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