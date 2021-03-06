<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Part\UpdatedAtTrait;

/**
 * @ORM\Table(name="withdraw")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Withdraw extends Operation
{
    const STATUS_NEW = 0;
    const STATUS_DONE = 1;
    const STATUS_REJECTED = 2;
    const STATUS_CANCELED = 3;

    use UpdatedAtTrait;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @var WithdrawConfirm|null
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\WithdrawConfirm", mappedBy="withdraw")
     */
    private $confirm;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status = self::STATUS_NEW;

    /**
     * @param User $user
     *
     * @return Withdraw
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
     * @param WithdrawConfirm $confirm
     *
     * @return Withdraw
     */
    public function setConfirm(WithdrawConfirm $confirm): self
    {
        $this->confirm = $confirm;

        return $this;
    }

    /**
     * @return WithdrawConfirm|null
     */
    public function getConfirm(): ?WithdrawConfirm
    {
        return $this->confirm;
    }

    /**
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return !empty($this->confirm);
    }

    /**
     * @param int $status
     *
     * @return Withdraw
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