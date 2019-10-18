<?php

namespace AppBundle\Entity\Lead;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Timer
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timeout_at", type="datetimetz", nullable=true)
     */
    private $timeoutAt;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", nullable=true)
     */
    private $action;

    /**
     * @param \DateTime $timeoutAt
     *
     * @return Timer
     */
    public function setTimeoutAt(\DateTime $timeoutAt): self
    {
        $this->timeoutAt = $timeoutAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTimeoutAt(): \DateTime
    {
        return $this->timeoutAt;
    }

    /**
     * @param string $action
     *
     * @return Timer
     */
    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }
}