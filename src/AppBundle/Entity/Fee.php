<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="fee")
 * @ORM\Entity
 */
class Fee extends Reason
{
    /**
     * @var Reason
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Reason")
     * @ORM\JoinColumn(name="reason_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $reason;

    /**
     * @param Reason $reason
     *
     * @return Fee
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
}