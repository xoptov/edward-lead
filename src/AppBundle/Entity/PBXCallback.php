<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Part\IdentificatorTrait;

/**
 * @ORM\Table(name="pbx_callback")
 * @ORM\Entity
 */
class PBXCallback implements IdentifiableInterface
{
    const EVENT_HANGUP = 'hangup';

    const DIRECTION_OUTGOING = 'outgoing';
    const DIRECTION_INCOMING = 'incoming';
    const DIRECTION_LOCAL    = 'local';

    const STATUS_ANSWER    = 'answer';
    const STATUS_BUSY      = 'busy';
    const STATUS_NO_ANSWER = 'no_answer';

    use IdentificatorTrait;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", nullable=true, length=6)
     */
    private $event;

    /**
     * @var PhoneCall|null
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\PhoneCall", inversedBy="callback")
     * @ORM\JoinColumn(name="phone_call_id", referencedColumnName="id")
     */
    private $phoneCall;

    /**
     * @var string|null
     *
     * @ORM\Column(name="src_phone_number", type="string", length=11, nullable=true)
     */
    private $srcPhoneNumber;

    /**
     * @var string|null
     *
     * @ORM\Column(name="dst_phone_number", type="string", length=11, nullable=true)
     */
    private $dstPhoneNumber;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="started_at", type="datetime")
     */
    private $startedAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="answer_at", type="datetime")
     */
    private $answerAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="completed_at", type="datetime")
     */
    private $completedAt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="direction", type="string", length=8)
     */
    private $direction;

    /**
     * @var string|null
     *
     * @ORM\Column(name="status", type="string", length=10)
     */
    private $status;

    /**
     * @var int|null
     *
     * @ORM\Column(name="duration", type="integer", options={"unsigned"="true"})
     */
    private $duration;

    /**
     * @var int|null
     *
     * @ORM\Column(name="billsec", type="integer", options={"unsigned"="true"})
     */
    private $billsec;

    /**
     * @var string|null
     *
     * @ORM\Column(name="recording", type="string", length=255)
     */
    private $recording;

    /**
     * @param string $event
     *
     * @return PBXCallback
     */
    public function setEvent(string $event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEvent(): ?string
    {
        return $this->event;
    }

    /**
     * @param PhoneCall|null $phoneCall
     *
     * @return PBXCallback
     */
    public function setPhoneCall(?PhoneCall $phoneCall): self
    {
        $this->phoneCall = $phoneCall;

        return $this;
    }

    /**
     * @return PhoneCall|null
     */
    public function getPhoneCall(): ?PhoneCall
    {
        return $this->phoneCall;
    }

    /**
     * @return bool
     */
    public function hasPhoneCall(): bool
    {
        return !empty($this->phoneCall);
    }

    /**
     * @param null|string $srcPhoneNumber
     *
     * @return PBXCallback
     */
    public function setSrcPhoneNumber(?string $srcPhoneNumber): self
    {
        $this->srcPhoneNumber = $srcPhoneNumber;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSrcPhoneNumber(): ?string
    {
        return $this->srcPhoneNumber;
    }

    /**
     * @param null|string $dstPhoneNumber
     *
     * @return PBXCallback
     */
    public function setDstPhoneNumber(?string $dstPhoneNumber): self
    {
        $this->dstPhoneNumber = $dstPhoneNumber;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDstPhoneNumber(): ?string
    {
        return $this->dstPhoneNumber;
    }

    /**
     * @param \DateTime $startedAt
     *
     * @return PBXCallback
     */
    public function setStartedAt(\DateTime $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
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
     * @return PBXCallback
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
     * @return PBXCallback
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
     * @param string $direction
     *
     * @return PBXCallback
     */
    public function setDirection(string $direction): self
    {
        $this->direction = $direction;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDirection(): ?string
    {
        return $this->direction;
    }

    /**
     * @param string $status
     *
     * @return PBXCallback
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
     * @param int $duration
     *
     * @return PBXCallback
     */
    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDuration(): ?int
    {
        return $this->duration;
    }

    /**
     * @param int $billsec
     *
     * @return PBXCallback
     */
    public function setBillsec(int $billsec): self
    {
        $this->billsec = $billsec;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getBillsec(): ?int
    {
        return $this->billsec;
    }

    /**
     * @param null|string $recording
     *
     * @return PBXCallback
     */
    public function setRecording(?string $recording): self
    {
        $this->recording = $recording;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getRecording(): ?string
    {
        return $this->recording;
    }

    /**
     * @return bool
     */
    public function isAnswered(): bool
    {
        return self::STATUS_ANSWER === $this->status;
    }
}