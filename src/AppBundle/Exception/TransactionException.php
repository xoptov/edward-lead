<?php

namespace AppBundle\Exception;

use AppBundle\Entity\MonetaryTransaction;
use Throwable;

class TransactionException extends FinancialException
{
    /**
     * @var MonetaryTransaction
     */
    private $transaction;

    /**
     * @param MonetaryTransaction $transaction
     * @param string              $message
     * @param int                 $code
     * @param Throwable|null      $previous
     */
    public function __construct(MonetaryTransaction $transaction, string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->transaction = $transaction;
    }
}