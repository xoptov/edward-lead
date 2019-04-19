<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="fee")
 * @ORM\Entity
 */
class Fee extends Operation
{
    /**
     * @var Operation
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Operation")
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
}