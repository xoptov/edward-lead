<?php

namespace AppBundle\Event;

use AppBundle\Entity\Trade;
use Symfony\Component\EventDispatcher\Event;

class TradeEvent extends Event
{
    const ARBITRAGE = 'trade.arbitrage';
    const ACCEPT    = 'trade.accept';
    const REJECT    = 'trade.reject';

    /**
     * @var Trade
     */
    private $trade;

    /**
     * @param Trade $trade
     */
    public function __construct(Trade $trade)
    {
        $this->trade = $trade;
    }

    /**
     * @return Trade
     */
    public function getTrade(): Trade
    {
        return $this->trade;
    }
}