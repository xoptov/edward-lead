<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="trade")
 * @ORM\Entity
 */
class Trade extends Reason
{
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Lead")
     * @ORM\JoinColumn(name="lead_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $lead;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint", options={"unsigned"="true"})
     */
    private $status;

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
}