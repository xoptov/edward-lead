<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="phone_call")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PhoneCallRepository")
 */
class PhoneCall extends Operation
{
    /**
     * @var string|null
     */
    private $externalId;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="caller_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $caller;

    /**
     * @var Lead|null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Lead")
     * @ORM\JoinColumn(name="lead_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $lead;

    /**
     * @var int|null
     *
     * @ORM\Column(name="duration_in_secs", type="integer", options={"unsigned": true})
     */
    private $durationInSecs;

    /**
     * @var \DateTime
     */
    private $startedAt;

    /**
     * @var \DateTime
     */
    private $answerAt;

    /**
     * @var \DateTime
     */
    private $completedAt;

    /**
     * @var string
     */
    private $status;

    /**
     * @var int|null
     */
    private $billSecs;

    /**
     * @var string|null
     */
    private $record;

    /**
     * @param string $externalId
     *
     * @return PhoneCall
     */
    public function setExternalId(string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    /**
     * @param User $caller
     *
     * @return PhoneCall
     */
    public function setCaller(User $caller): self
    {
        $this->caller = $caller;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getCaller(): ?User
    {
        return $this->caller;
    }

    /**
     * @param Lead $lead
     *
     * @return PhoneCall
     */
    public function setLead(Lead $lead): self
    {
        $this->lead = $lead;

        return $this;
    }

    /**
     * @return Lead|null
     */
    public function getLead(): ?Lead
    {
        return $this->lead;
    }

    /**
     * @param int $durationInSecs
     *
     * @return PhoneCall
     */
    public function setDurationInSecs(int $durationInSecs): self
    {
        $this->durationInSecs = $durationInSecs;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDurationInSecs(): ?int
    {
        return $this->durationInSecs;
    }

    /**
     * @param \DateTime $startedAt
     */
    public function setStartedAt(\DateTime $startedAt): void
    {
        $this->startedAt = $startedAt;
    }

    /**
     * @return \DateTime
     */
    public function getStartedAt(): \DateTime
    {
        return $this->startedAt;
    }

    /**
     * @param \DateTime $answerAt
     */
    public function setAnswerAt(\DateTime $answerAt): void
    {
        $this->answerAt = $answerAt;
    }

    /**
     * @param \DateTime $completedAt
     */
    public function setCompletedAt(\DateTime $completedAt): void
    {
        $this->completedAt = $completedAt;
    }

    /**
     * @return \DateTime
     */
    public function getCompletedAt(): \DateTime
    {
        return $this->completedAt;
    }

    /**
     * @param string $status
     *
     * @return PhoneCall
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param int $billSecs
     *
     * @return PhoneCall
     */
    public function setBillSecs(int $billSecs): self
    {
        $this->billSecs = $billSecs;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getBillSecs(): ?int
    {
        return $this->billSecs;
    }

    /**
     * @param string $record
     *
     * @return PhoneCall
     */
    public function setRecord(string $record): self
    {
        $this->record = $record;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRecord(): ?string
    {
        return $this->record;
    }
}