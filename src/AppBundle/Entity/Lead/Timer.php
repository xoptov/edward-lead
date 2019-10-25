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
     * @var bool
     *
     * @ORM\Column(name="processed", type="boolean", nullable=true)
     */
    private $processed;

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

    /**
     * @param bool $value
     *
     * @return Timer
     */
    public function setProcessed(bool $value): self
    {
        $this->processed = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isProcessed(): bool
    {
        return $this->processed;
    }
}