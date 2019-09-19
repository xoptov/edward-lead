<?php

namespace AppBundle\Exception;

use Throwable;
use AppBundle\Entity\User;
use AppBundle\Entity\Trade;

class RequestCallException extends \Exception
{
    /**
     * @var User
     */
    private $caller;

    /**
     * @var Trade
     */
    private $trade;

    /**
     * @param User           $caller
     * @param Trade          $trade
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(User $caller, Trade $trade, string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->caller = $caller;
        $this->trade = $trade;
    }

    /**
     * @return User
     */
    public function getCaller(): User
    {
        return $this->caller;
    }

    /**
     * @return Trade
     */
    public function getTrade(): Trade
    {
        return $this->trade;
    }
}