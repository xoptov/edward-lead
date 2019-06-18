<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="phone_call")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PhoneCallRepository")
 */
class PhoneCall extends Operation
{
    const STATUS_NEW       = 'new';
    const STATUS_REQUESTED = 'requested';
    const STATUS_ANSWER    = 'answer';
    const STATUS_BUSY      = 'busy';
    const STATUS_NO_ANSWER = 'no_answer';
    const STATUS_ERROR     = 'error';

    /**
     * @var string|null
     *
     * @ORM\Column(name="external_id", type="string", length=16)
     */
    private $externalId;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="caller_id", referencedColumnName="id")
     */
    private $caller;

    /**
     * @var Lead|null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Lead")
     * @ORM\JoinColumn(name="lead_id", referencedColumnName="id")
     */
    private $lead;

    /**
     * @var int|null
     *
     * @ORM\Column(name="duration_in_secs", type="integer", nullable=true, options={"unsigned": true})
     */
    private $durationInSecs;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="started_at", type="datetime", nullable=true)
     */
    private $startedAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="answer_at", type="datetime", nullable=true)
     */
    private $answerAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="completed_at", type="datetime", nullable=true)
     */
    private $completedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=9, nullable=true)
     */
    private $status = self::STATUS_NEW;

    /**
     * @var int|null
     *
     * @ORM\Column(name="bill_secs", type="integer", nullable=true)
     */
    private $billSecs;

    /**
     * @var string|null
     *
     * @ORM\Column(name="record", type="string", nullable=true)
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
     * @return string
     */
    public function getCallerPhone(): string
    {
        return $this->caller->getOfficePhone();
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
     * @return string
     */
    public function getLeadPhone(): string
    {
        return $this->lead->getPhone();
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
     * @return \DateTime|null
     */
    public function getStartedAt(): ?\DateTime
    {
        return $this->startedAt;
    }

    /**
     * @param \DateTime $answerAt
     *
     * @return PhoneCall
     */
    public function setAnswerAt(\DateTime $answerAt): self
    {
        $this->answerAt = $answerAt;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getAnswerAt(): ?\DateTime
    {
        return $this->answerAt;
    }

    /**
     * @param \DateTime $completedAt
     *
     * @return PhoneCall
     */
    public function setCompletedAt(\DateTime $completedAt): self
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getCompletedAt(): ?\DateTime
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