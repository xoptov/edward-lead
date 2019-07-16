<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Part\CreatedAtTrait;
use AppBundle\Entity\Part\IdentificatorTrait;

/**
 * @ORM\Table(name="monetary_hold")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class MonetaryHold implements IdentifiableInterface
{
    use IdentificatorTrait;

    use CreatedAtTrait;

    /**
     * @var ClientAccount
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ClientAccount")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $account;

    /**
     * @var Operation
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Operation", inversedBy="hold")
     * @ORM\JoinColumn(name="operation_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $operation;

    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="integer", options={"unsigned"="false"})
     */
    private $amount;

    /**
     * @param ClientAccount $account
     *
     * @return MonetaryHold
     */
    public function setAccount(ClientAccount $account): self
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return ClientAccount
     */
    public function getAccount(): ClientAccount
    {
        return $this->account;
    }

    /**
     * @param Operation $operation
     *
     * @return MonetaryHold
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
     * @param int $amount
     *
     * @return MonetaryHold
     */
    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @param int $divisor
     *
     * @return float
     */
    public function getHumanAmount(?int $divisor = 100): float
    {
        return $this->amount / $divisor;
    }
}