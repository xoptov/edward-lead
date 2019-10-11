<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="fee")
 * @ORM\Entity
 */
class Fee extends Operation
{
    const STATUS_NEW = 0;
    const STATUS_PROCESSED = 1;

    /**
     * @var Operation
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Operation", inversedBy="fees")
     * @ORM\JoinColumn(name="operation_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $operation;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="payer_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $payer;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint", options={"unsigned":"true"})
     */
    private $status = self::STATUS_NEW;

    /**
     * @param Operation $operation
     *
     * @return Fee
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
     * @param User $payer
     *
     * @return Fee
     */
    public function setPayer(User $payer): self
    {
        $this->payer = $payer;

        return $this;
    }

    /**
     * @return User
     */
    public function getPayer(): User
    {
        return $this->payer;
    }

    /**
     * @return ClientAccount
     */
    public function getPayerAccount(): ClientAccount
    {
        return $this->payer->getAccount();
    }

    /**
     * @param int $status
     *
     * @return Fee
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return self::STATUS_NEW === $this->status;
    }

    /**
     * @return bool
     */
    public function isProcessed(): bool
    {
        return self::STATUS_PROCESSED === $this->status;
    }
}