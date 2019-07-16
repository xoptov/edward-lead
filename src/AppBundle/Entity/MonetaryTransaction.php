<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Part\CreatedAtTrait;
use AppBundle\Entity\Part\IdentificatorTrait;

/**
 * @ORM\Table(name="monetary_transaction")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class MonetaryTransaction implements IdentifiableInterface
{
    use IdentificatorTrait;

    use CreatedAtTrait;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Account")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $account;

    /**
     * @var Operation
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Operation")
     * @ORM\JoinColumn(name="operation_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $operation;

    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

    /**
     * @var bool
     *
     * @ORM\Column(name="processed", type="boolean")
     */
    private $processed = false;

    /**
     * @param Account $account
     *
     * @return MonetaryTransaction
     */
    public function setAccount(Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return Account
     */
    public function getAccount(): Account
    {
        return $this->account;
    }

    /**
     * @param Operation $operation
     *
     * @return MonetaryTransaction
     */
    public function setOperation(Operation $operation): self
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * @return Operation
     */
    public function getOperation(): Operation
    {
        return $this->operation;
    }

    /**
     * @param integer $amount
     *
     * @return MonetaryTransaction
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int|null $divisor
     *
     * @return float
     */
    public function getHumanAmount(?int $divisor = 100): float
    {
        return $this->amount / $divisor;
    }

    /**
     * @param bool $processed
     *
     * @return MonetaryTransaction
     */
    public function setProcessed(bool $processed): self
    {
        $this->processed = $processed;

        return $this;
    }

    /**
     * @return bool
     */
    public function isProcessed(): bool
    {
        return $this->processed;
    }
}
