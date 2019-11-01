<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="referrer_reward")
 * @ORM\Entity
 */
class ReferrerReward extends Operation
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
     * @ORM\JoinColumn(name="referrer_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $referrer;

    /**
     * @param Operation $operation
     *
     * @return ReferrerReward
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
     * @param User $referrer
     *
     * @return ReferrerReward
     */
    public function setReferrer(User $referrer): self
    {
        $this->referrer = $referrer;

        return $this;
    }

    /**
     * @return User
     */
    public function getReferrer(): User
    {
        return $this->referrer;
    }

    /**
     * @return Account
     */
    public function getReferrerAccount(): Account
    {
        return $this->referrer->getAccount();
    }
}
