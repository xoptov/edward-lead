<?php

namespace AppBundle\Exception;

use Throwable;
use AppBundle\Entity\ClientAccount;

class InsufficientFundsException extends FinancialException
{
    /**
     * @var ClientAccount
     */
    private $account;

    /**
     * @var int
     */
    private $needle;

    /**
     * @param ClientAccount  $account
     * @param int            $needle
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(ClientAccount $account, int $needle, string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->account = $account;
        $this->needle = $needle;
    }

    /**
     * @return ClientAccount
     */
    public function getAccount(): ClientAccount
    {
        return $this->account;
    }

    /**
     * @return int
     */
    public function getNeedle(): int
    {
        return $this->needle;
    }
}