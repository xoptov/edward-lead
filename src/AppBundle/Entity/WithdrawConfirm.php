<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class WithdrawConfirm extends OperationConfirm
{
    /**
     * @var Withdraw
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Withdraw", inversedBy="confirm")
     * @ORM\JoinColumn(name="withdraw_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $withdraw;

    /**
     * @param Withdraw $withdraw
     *
     * @return WithdrawConfirm
     */
    public function setWithdraw(Withdraw $withdraw): self
    {
        $this->withdraw = $withdraw;

        return $this;
    }

    /**
     * @return Withdraw
     */
    public function getWithdraw(): Withdraw
    {
        return $this->withdraw;
    }
}