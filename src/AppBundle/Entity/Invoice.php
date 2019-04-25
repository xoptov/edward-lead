<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="invoice")
 * @ORM\Entity
 */
class Invoice extends Operation
{
    const STATUS_NEW = 0;
    const STATUS_DONE = 1;
    const STATUS_CANCELED = 2;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint", options={"unsigned"="true"})
     */
    private $status = self::STATUS_NEW;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @param User $user
     *
     * @return Invoice
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @return ClientAccount|null
     */
    public function getAccount(): ?ClientAccount
    {
        return $this->user->getAccount();
    }

    /**
     * @param int $status
     *
     * @return Invoice
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
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return bool
     */
    public function isNotProcessed(): bool
    {
        return self::STATUS_NEW === $this->status;
    }

    /**
     * @return bool
     */
    public function isProcessed(): bool
    {
        return !$this->isNotProcessed();
    }
}