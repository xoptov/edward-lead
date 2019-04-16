<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="hold")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Hold
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"="true"})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var ClientAccount
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ClientAccount")
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
     * @ORM\Column(name="amount", type="integer", options={"unsigned"="false"})
     */
    private $amount;

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
     * @param ClientAccount $account
     *
     * @return Hold
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
     * @param Reason $reason
     * @return Hold
     */
    public function setReason(Reason $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * @return Reason
     */
    public function getReason(): Reason
    {
        return $this->reason;
    }

    /**
     * @param int $amount
     *
     * @return Hold
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
     * @param \DateTime $createdAt
     *
     * @return Hold
     */
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}