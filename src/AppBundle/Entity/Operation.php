<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", options={"unsigned"="true"})
     */
    protected $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=150, nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="integer", options={"unsigned": true})
     */
    private $amount;

    /**
     * @var MonetaryHold
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\MonetaryHold", mappedBy="operation")
     */
    private $hold;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

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
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist(): void
    {
        $this->createdAt = new \DateTime();
    }
}