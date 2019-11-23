<?php

class Money
{
    const PENNIES_IN_RUBLE = 100;

    /**
     * @var int
     */
    private $rubles = 0;

    /**
     * @var int
     */
    private $pennies = 0;

    /**
     * @param float $value
     * 
     * @return Money
     */
    public static function createFromFloat(float $value): self
    {
        $rubles = intval($value);
        $pennies = $value * self::PENNIES_IN_RUBLE % self::PENNIES_IN_RUBLE;

        return new Money($rubles, $pennies);
    }

    /**
     * @param int $value
     * 
     * @return Money
     */
    public static function createFromInteger(int $value): self
    {
        $rubles = intdiv($value, self::PENNIES_IN_RUBLE);
        $pennies = $value % self::PENNIES_IN_RUBLE;

        return new Money($rubles, $pennies);
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
     * @return int
     */
    public function inPennies(): int
    {
        return $this->rubles * self::PENNIES_IN_RUBLE + $this->pennies;
    }

    /**
     * @param Money $money
     * 
     * @return Money
     */
    public function add(Money $money): self
    {
        $value1 = $this->inPennies();
        $value2 = $money->inPennies();

        $this->setPennies($value1 + $value2);
        
        return $this;
    }

    /**
     * @param float $value
     * 
     * @return Money
     */
    public function addFloat(float $value): self
    {
        $money = Money::createFromFloat($value);

        return $this->add($money);
    }

    /**
     * @param int $value
     * 
     * @return Money
     */
    public function addInteger(int $value): self
    {
        $money = Money::createFromInteger($value);

        return $this->add($money);
    }

    /**
     * @param Money $money
     */
    public function substract(Money $money): self
    {
        $value1 = $this->inPennies();
        $value2 = $money->inPennies();

        $this->setPennies($value1 - $value2);

        return $this;
    }

    /**
     * @param float $value
     * 
     * @return Money
     */
    public function substractFloat(float $value): self
    {
        $money = Money::createFromFloat($value);
        $this->substract($money);

        return $this;
    }

    /**
     * @param int $value
     * 
     * @return Money
     */
    public function substractInteger(int $value): self
    {
        $money = Money::createFromInteger($value);
        $this->substract($money);

        return $this;
    }

    /**
     * @param int $pennies
     */
    private function setPennies(int $pennies): void
    {
        $rubles = intdiv($pennies, self::PENNIES_IN_RUBLE);

        if ($rubles) {
            $this->rubles += $rubles;
        }

        $this->pennies = $pennies % self::PENNIES_IN_RUBLE;
    }
}
