<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="operation")
 * @ORM\Entity
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
    private $id;

    /**
     * @var SystemAccount|ClientAccount
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SystemAccount")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $account;

    /**
     * @var Reason
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Reason")
     * @ORM\JoinColumn(name="reason_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $reason;

    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param ClientAccount|SystemAccount $account
     *
     * @return Operation
     */
    public function setAccount(SystemAccount $account): self
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return ClientAccount|SystemAccount
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param Reason $reason
     *
     * @return Operation
     */
    public function setReason(Reason $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return Operation
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Operation
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}

