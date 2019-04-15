<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="call")
 * @ORM\Entity
 */
class Call extends Reason
{
    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="caller_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $caller;

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
     * @ORM\Column(name="duration_in_secs", type="integer", options={"unsigned": true})
     */
    private $durationInSecs;

    /**
     * @param User $caller
     *
     * @return Call
     */
    public function setCaller(User $caller): self
    {
        $this->caller = $caller;

        return $this;
    }

    /**
     * @return User
     */
    public function getCaller(): User
    {
        return $this->caller;
    }

    /**
     * @param Lead $lead
     *
     * @return Call
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
     * @param int $durationInSecs
     *
     * @return Call
     */
    public function setDurationInSecs(int $durationInSecs): self
    {
        $this->durationInSecs = $durationInSecs;

        return $this;
    }

    /**
     * @return int
     */
    public function getDurationInSecs(): int
    {
        return $this->durationInSecs;
    }
}