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
     * @var \DateTime|null
     *
     * @ORM\Column(name="processed_at", type="datetime", nullable=true)
     */
    private $processedAt;

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
     * @param \DateTime $processedAt
     *
     * @return Timer
     */
    public function setProcessedAt(\DateTime $processedAt): self
    {
        $this->processedAt = $processedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getProcessedAt(): \DateTime
    {
        return $this->processedAt;
    }

    /**
     * @return bool
     */
    public function isProcessed(): bool
    {
        return !empty($this->processedAt);
    }
}