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
    const STATUS_PROCESSED = 'processed';
    const STATUS_ERROR     = 'error';

    /**
     * @var string|null
     *
     * @ORM\Column(name="external_id", type="string", length=16, nullable=true)
     */
    private $externalId;

    /**
     * @var PBXCallback|null
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\PBXCallback", mappedBy="phoneCall")
     */
    private $callback;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="caller_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $caller;

    /**
     * @var Lead|null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Lead")
     * @ORM\JoinColumn(name="lead_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $lead;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=9, nullable=true)
     */
    private $status = self::STATUS_NEW;

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
     * @param PBXCallback|null $callback
     *
     * @return PhoneCall
     */
    public function setCallback(?PBXCallback $callback): self
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * @return PBXCallback|null
     */
    public function getCallback(): ?PBXCallback
    {
        return $this->callback;
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
     * @return bool
     */
    public function isProcessed(): bool
    {
        return self::STATUS_PROCESSED === $this->status;
    }
}