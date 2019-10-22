<?php

namespace AppBundle\Entity\Lead;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Timer
{
    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="end_at", type="datetime", nullable=true)
     */
    private $endAt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="action", type="string", nullable=true)
     */
    private $action;

    /**
     * @param \DateTime $endAt
     *
     * @return Timer
     */
    public function setEndAt(\DateTime $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getEndAt(): ?\DateTime
    {
        return $this->endAt;
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
     * @return string|null
     */
    public function getAction(): ?string
    {
        return $this->action;
    }
}