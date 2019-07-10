<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Part\CreatedAtTrait;
use AppBundle\Entity\Part\IdentificatorTrait;

/**
 * @ORM\Table(name="operation")
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string", length=15)
 * @ORM\DiscriminatorMap({
 *     "operation" = "Operation",
 *     "invoice" = "Invoice",
 *     "withdraw" = "Withdraw",
 *     "fee" = "Fee",
 *     "referrer_reward" = "ReferrerReward",
 *     "phone_call" = "PhoneCall",
 *     "trade" = "Trade"
 * })
 * @ORM\HasLifecycleCallbacks
 */
class Operation
{
    use IdentificatorTrait;

    use CreatedAtTrait;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=150, nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="integer", nullable=true, options={"unsigned": true})
     */
    private $amount;

    /**
     * @var MonetaryHold
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\MonetaryHold", mappedBy="operation")
     */
    private $hold;

    /**
     * @param null|string $description
     *
     * @return self
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param int $amount
     *
     * @return self
     */
    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @param int|null $divisor
     *
     * @return int|null
     */
    public function getAmount(?int $divisor = null): ?int
    {
        if ($divisor) {
            return $this->amount / $divisor;
        }

        return $this->amount;
    }

    /**
     * @param MonetaryHold|null $hold
     *
     * @return Operation
     */
    public function setHold(?MonetaryHold $hold): self
    {
        $this->hold = $hold;

        return $this;
    }

    /**
     * @return MonetaryHold|null
     */
    public function getHold(): ?MonetaryHold
    {
        return $this->hold;
    }

    /**
     * @return bool
     */
    public function hasHold(): bool
    {
        return !empty($this->hold);
    }

    /**
     * @return MonetaryHold|null
     */
    public function takeHold(): ?MonetaryHold
    {
        $hold = $this->hold;
        $this->hold = null;

        return $hold;
    }
}