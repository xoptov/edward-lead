<?php

use AppBundle\Util\Formatter;

class Money
{
    /**
     * @var int
     */
    private $rubles = 0;

    /**
     * @var int
     */
    private $pennies = 0;

    public static function createFromFloat(float $amount): self
    {
        //todo: Необходимо имплементировать логику создания.
    }

    public static function createFromInteger(int $amount): self
    {
        //todo: Необходимо имплементировать логику создания.
    }

    /**
     * @param int $rubles
     * @param int $pennies
     */
    public function __construct(int $rubles = 0, int $pennies = 0)
    {
        $this->rubles = $rubles;
        $this->setPennies($pennies);
    }

    /**
     * @return int
     */
    public function getRubles(): int
    {
        return $this->rubles;
    }

    /**
     * @return int
     */
    public function getPennies(): int
    {
        return $this->pennies;
    }

    /**
     * @param Money $money
     * 
     * @return Money
     */
    public function addAsMoney(Money $money): self
    {
    
        return $this;
    }

    /**
     * @param float $amount
     * 
     * @return Money
     */
    public function addAsFloat(flaot $amount): self
    {
        $rubles = intval($amount);
        $pennies = $amount % 1;

        $money = new Money($rubles, $pennies);

        return $this->addAsMoney($money);
    }

    /**
     * @param int $amount
     * 
     * @return Money
     */
    public function addAsInteger(int $amount): self
    {
        $rubles = intdiv($amount, Formatter::MONEY_DIVISOR);
        $pennies = $amount % Formatter::MONEY_DIVISOR;

        $money = new Money($rubles, $pennies);

        return $this->addAsMoney($money);
    }

    /**
     * @param int $pennies
     * 
     * @return Money
     */
    public function addPennies(int $pennies): self
    {
        $this->addRublesByPennies($pennies);
        $this->pennies += $pennies % Formatter::MONEY_DIVISOR;

        return $this;
    }

    /**
     * @param int $pennies
     * @param int $round
     */
    private function setPennies(int $pennies): self
    {
        $this->addRublesByPennies($pennies);
        $this->pennies = $pennies % Formatter::MONEY_DIVISOR;

        return $this;
    }

    /**
     * @param int $pennies
     */
    private function addRublesByPennies(int $pennies): void
    {
        $rubles = intdiv($pennies, Formatter::MONEY_DIVISOR);

        if ($rubles) {
            $this->rubles += $rubles;
        }
    }
}
