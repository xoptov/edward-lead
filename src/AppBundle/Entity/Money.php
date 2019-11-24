<?php

namespace AppBundle\Entity;

use RuntimeException;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
final class Money
{
    const PENNIES_IN_RUBLE = 100;

    /**
     * @var int
     * 
     * @ORM\Column(type="bigint")
     */
    private $value = 0;

    /**
     * @param float $value
     * 
     * @return Money
     */
    public static function createFromFloat(float $value): self
    {
        return new self(intval($value * self::PENNIES_IN_RUBLE));
    }

    /**
     * @param Money|float|int $value
     * 
     * @return Money
     */
    public static function create($value): self
    {
        if ($value instanceof self) {
            return $value;
        }
        
        if (is_float($value)) {
            return self::createFromFloat($value);
        }
        
        if (is_integer($value)) {
            return new self($value);
        }

        throw new RuntimeException('Тип значение не поддреживается');
    }

    /**
     * @param int $value
     */
    public function __construct(int $value = 0)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getRubles(): int
    {
        return abs(intdiv($this->value, self::PENNIES_IN_RUBLE));
    }

    /**
     * @return int
     */
    public function getPennies(): int
    {
        return abs($this->value % self::PENNIES_IN_RUBLE);
    }

    /**
     * @param Money|float|int $money
     * 
     * @return Money
     */
    public function add($value): self
    {
        $operand = self::create($value);

        return new self($this->getValue() + $operand->getValue());
    }

    /**
     * @param Money|float|int $money
     */
    public function substract($value): self
    {
        $operand = self::create($value);

        return new self($this->getValue() - $operand->getValue());
    }

    /**
     * @param float $value
     * 
     * @return Money
     */
    public function multiple(float $value): self
    {
        return new self($this->getValue() * $value);
    }

    /**
     * @param Money|float|int $value
     * 
     * @return float
     */
    public function divide($value): float
    {
        $operand = self::create($value);

        return $this->getValue() / $operand->getValue();
    }

    /**
     * @param Money|float|int
     */
    public function isEqual($value): bool
    {
        $operand = self::create($value);

        return $this->getValue() === $operand->getValue();
    }

    /**
     * @param Money|float|int
     * 
     * @return bool
     */
    public function isLess($value): bool
    {
        $operand = self::create($value);

        return $this->getValue() < $operand->getValue();
    }

    /**
     * @param Money|float|int
     * 
     * @return bool
     */
    public function isGreater($value): bool
    {
        $operand = self::create($value);

        return $this->getValue() > $operand->getValue();
    }

    /**
     * @param Money|float|int
     * 
     * @return bool
     */
    public function isLessOrEqual($value): bool
    {
        return $this->isLess($value) || $this->isEqual($value);
    }

    /**
     * @param Money|float|int
     * 
     * @return bool
     */
    public function isGreaterOrEqual($value): bool
    {
        return $this->isGreater($value) || $this->isEqual($value);
    }

    /**
     * @param Money|float|int
     * 
     * @return int
     */
    public function compare($value): int
    {
        if ($this->isLess($value)) {
            return -1;
        }
        
        if ($this->isGreater($value)) {
            return 1;
        }
        
        return 0;
    }

    /**
     * @return bool
     */
    public function isNegative(): bool
    {
        return $this->value < 0;
    }
}
