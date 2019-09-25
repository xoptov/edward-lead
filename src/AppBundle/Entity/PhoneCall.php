<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\PBX\Callback;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="phone_call")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PhoneCallRepository")
 */
class PhoneCall extends Operation
{
    const STATUS_NEW       = 'new';
    const STATUS_REQUESTED = 'requested';
    const STATUS_PROCESSED = 'processed';
    const STATUS_ERROR     = 'error';

    const RESULT_SUCCESS = 1;
    const RESULT_FAIL    = 2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="external_id", type="string", length=16)
     */
    private $externalId;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PBX\Callback", mappedBy="phoneCall")
     */
    private $callbacks;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="caller_id", referencedColumnName="id", nullable=false)
     */
    private $caller;

    /**
     * @var Trade
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Trade", inversedBy="phoneCalls")
     * @ORM\JoinColumn(name="trade_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $trade;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=9, nullable=true)
     */
    private $status = self::STATUS_NEW;

    /**
     * @var int
     *
     * @ORM\Column(name="result", type="smallint", nullable=true, options={"unsigned":"true"})
     */
    private $result;

    /**
     * PhoneCall constructor.
     */
    public function __construct()
    {
        $this->callbacks = new ArrayCollection();
    }

    /**
     * @param null|string $externalId
     *
     * @return PhoneCall
     */
    public function setExternalId(?string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    /**
     * @return ArrayCollection
     */
    public function getCallbacks()
    {
        return $this->callbacks;
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
     * @param Trade $trade
     *
     * @return PhoneCall
     */
    public function setTrade(Trade $trade): self
    {
        $this->trade = $trade;

        return $this;
    }

    /**
     * @return Trade
     */
    public function getTrade(): Trade
    {
        return $this->trade;
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
     * @param int|null $result
     *
     * @return PhoneCall
     */
    public function setResult(?int $result): self
    {
        $this->result = $result;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getResult(): ?int
    {
        return $this->result;
    }

    /**
     * @return bool
     */
    public function isProcessed(): bool
    {
        return self::STATUS_PROCESSED === $this->status;
    }

    /**
     * @return null|string
     */
    public function getLeadPhone(): ?string
    {
        if ($this->trade) {
            return $this->trade->getLeadPhone();
        }

        return null;
    }

    /**
     * @return Callback|null
     */
    public function getLastCallback(): ?Callback
    {
        return $this->callbacks->last();
    }

    /**
     * @return int
     */
    public function getTalkDuration()
    {
        $duration = 0;

        /** @var Callback $callback */
        foreach ($this->callbacks as $callback)
        {
            $duration += $callback->getFirstShoulder()->getDurationInSecond();
        }

        return $duration;
    }

    /**
     * @return null|string
     */
    public function getLastAudioRecord(): ?string
    {
        $lastCallback = $this->getLastCallback();

        if ($lastCallback) {
            return $lastCallback->getAudioRecord();
        }

        return null;
    }
}