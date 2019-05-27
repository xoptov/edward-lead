<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="trade")
 * @ORM\Entity
 */
class Trade extends Operation
{
    const STATUS_NEW = 0;
    const STATUS_ACCEPTED = 1;
    const STATUS_REJECTED = 2;
    const STATUS_CANCELED = 3;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="buyer_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $buyer;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="seller_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $seller;

    /**
     * @var Lead
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Lead", inversedBy="trade")
     * @ORM\JoinColumn(name="lead_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $lead;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint", options={"unsigned"="true"})
     */
    private $status = self::STATUS_NEW;

    /**
     * @param User $buyer
     *
     * @return Trade
     */
    public function setBuyer(User $buyer): self
    {
        $this->buyer = $buyer;

        return $this;
    }

    /**
     * @return User
     */
    public function getBuyer(): User
    {
        return $this->buyer;
    }

    /**
     * @return ClientAccount|null
     */
    public function getBuyerAccount(): ?ClientAccount
    {
        return $this->buyer->getAccount();
    }

    /**
     * @param User $seller
     *
     * @return Trade
     */
    public function setSeller(User $seller): self
    {
        $this->seller = $seller;

        return $this;
    }

    /**
     * @return User
     */
    public function getSeller(): User
    {
        return $this->seller;
    }

    /**
     * @return ClientAccount|null
     */
    public function getSellerAccount(): ?ClientAccount
    {
        return $this->seller->getAccount();
    }

    /**
     * @param Lead $lead
     *
     * @return Trade
     */
    public function setLead(Lead $lead): self
    {
        $this->lead = $lead;

        return $this;
    }

    /**
     * @return Lead
     */
    public function getLead(): Lead
    {
        return $this->lead;
    }

    /**
     * @param int $status
     *
     * @return Trade
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function isProcessed(): bool
    {
        return in_array($this->status, [self::STATUS_ACCEPTED, self::STATUS_REJECTED, self::STATUS_CANCELED]);
    }
}