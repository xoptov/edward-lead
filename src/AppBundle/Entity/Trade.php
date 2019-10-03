<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="trade")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TradeRepository")
 */
class Trade extends Operation
{
    const STATUS_NEW        = 0;
    const STATUS_ACCEPTED   = 1;
    const STATUS_REJECTED   = 2;
    const STATUS_PROCEEDING = 3;
    const STATUS_CALL_BACK  = 4;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="buyer_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $buyer;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="seller_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $seller;

    /**
     * @var Lead
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Lead", inversedBy="trade")
     * @ORM\JoinColumn(name="lead_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $lead;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PhoneCall", mappedBy="trade")
     */
    private $phoneCalls;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\PhoneCall")
     * @ORM\JoinTable(
     *     name="trades_ask_callback_phone_calls",
     *     joinColumns={
     *          @ORM\JoinColumn(name="trade_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *     },
     *     inverseJoinColumns={
     *          @ORM\JoinColumn(name="phone_call_id", referencedColumnName="id", unique=true, nullable=false, onDelete="CASCADE")
     *     }
     * )
     */
    private $askCallbackPhoneCalls;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint", options={"unsigned"="true"})
     */
    private $status = self::STATUS_NEW;

    /**
     * Trade constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->phoneCalls = new ArrayCollection();
        $this->askCallbackPhoneCalls = new ArrayCollection();
    }

    /**
     * @param User $buyer
     *
     * @return Trade
     */
    public function setBuyer(User $buyer): self
    {
        $this->buyer = $buyer;

        return $this;
    }

    /**
     * @return User
     */
    public function getBuyer(): User
    {
        return $this->buyer;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function isBuyer(User $user): bool
    {
        return $this->getBuyer() === $user;
    }

    /**
     * @return int|null
     */
    public function getBuyerId(): ?int
    {
        if ($this->buyer) {
            return $this->buyer->getId();
        }

        return null;
    }

    /**
     * @return ClientAccount|null
     */
    public function getBuyerAccount(): ?ClientAccount
    {
        return $this->buyer->getAccount();
    }

    /**
     * @param User $seller
     *
     * @return Trade
     */
    public function setSeller(User $seller): self
    {
        $this->seller = $seller;

        return $this;
    }

    /**
     * @return User
     */
    public function getSeller(): User
    {
        return $this->seller;
    }

    /**
     * @return int|null
     */
    public function getSellerId(): ?int
    {
        if ($this->seller) {
            return $this->seller->getId();
        }

        return null;
    }

    /**
     * @return ClientAccount|null
     */
    public function getSellerAccount(): ?ClientAccount
    {
        return $this->seller->getAccount();
    }

    /**
     * @param Lead $lead
     *
     * @return Trade
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
     * @return int|null
     */
    public function getLeadId(): ?int
    {
        if ($this->lead) {
            return $this->lead->getId();
        }

        return null;
    }

    /**
     * @return null|string
     */
    public function getLeadName(): ?string
    {
        if ($this->lead) {
            return $this->lead->getName();
        }

        return null;
    }

    /**
     * @return null|string
     */
    public function getLeadPhone(): ?string
    {
        if ($this->lead) {
            return $this->lead->getPhone();
        }

        return null;
    }

    /**
     * @param PhoneCall $phoneCall
     *
     * @return bool
     */
    public function addPhoneCall(PhoneCall $phoneCall): bool
    {
        return $this->phoneCalls->add($phoneCall);
    }

    /**
     * @return Collection
     */
    public function getPhoneCalls(): Collection
    {
        return $this->phoneCalls;
    }

    /**
     * @return PhoneCall|false
     */
    public function getLastPhoneCall()
    {
        return $this->phoneCalls->last();
    }

    /**
     * @return int
     */
    public function getAskCallbackCount(): int
    {
        return $this->askCallbackPhoneCalls->count();
    }

    /**
     * @param PhoneCall $phoneCall
     *
     * @return bool
     */
    public function hasAskCallbackPhoneCall(PhoneCall $phoneCall): bool
    {
        return $this->askCallbackPhoneCalls->contains($phoneCall);
    }

    /**
     * @param PhoneCall $phoneCall
     *
     * @return bool
     */
    public function addAskCallbackPhoneCall(PhoneCall $phoneCall): bool
    {
        if ($this->askCallbackPhoneCalls->contains($phoneCall)) {
            return false;
        }

        return $this->askCallbackPhoneCalls->add($phoneCall);
    }

    /**
     * @param int $status
     *
     * @return Trade
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return self::STATUS_NEW === $this->status;
    }

    /**
     * @return bool
     */
    public function isAccepted(): bool
    {
        return self::STATUS_ACCEPTED === $this->status;
    }

    /**
     * @return bool
     */
    public function isRejected(): bool
    {
        return self::STATUS_REJECTED === $this->status;
    }

    /**
     * @return bool
     */
    public function isProceeding(): bool
    {
        return self::STATUS_PROCEEDING === $this->status;
    }

    /**
     * @return bool
     */
    public function isCallback(): bool
    {
        return self::STATUS_CALL_BACK === $this->status;
    }

    /**
     * @return bool
     */
    public function isProcessed(): bool
    {
        return $this->isAccepted() || $this->isRejected();
    }
}