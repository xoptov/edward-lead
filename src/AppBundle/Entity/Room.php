<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Part\EnabledTrait;
use AppBundle\Entity\Part\TimeTrackableTrait;
use AppBundle\Entity\Part\IdentificatorTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="room",options={"auto_increment"="1000"})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RoomRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Room implements IdentifiableInterface
{
    use IdentificatorTrait;

    use TimeTrackableTrait;

    use EnabledTrait;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(message="Название комнаты должно быть указано")
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(message="Сфера должна быть указана")
     *
     * @ORM\Column(name="sphere", type="string")
     */
    private $sphere;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(message="Необходимо указать критерии")
     * @Assert\Length(max=1000, maxMessage="Максимальное количество символов 1000")
     *
     * @ORM\Column(name="lead_criteria", type="text", nullable=true)
     */
    private $leadCriteria;

    /**
     * @var int|null
     *
     * @Assert\Range(min="100", minMessage="Минимальная стоимость 1", max="999900", maxMessage="Максимальная стоимость 9999")
     *
     * @ORM\Column(name="lead_price", type="integer", nullable=true, options={"unsigned":"true"})
     */
    private $leadPrice;

    /**
     * @var bool
     *
     * @Assert\NotNull(message="Необходимо указать использование гарантии")
     *
     * @ORM\Column(name="platform_warranty", type="boolean")
     */
    private $platformWarranty = false;

    /**
     * @var float|null
     *
     * @Assert\GreaterThanOrEqual(value="0", message="Комиссия должна быть положительной")
     *
     * @ORM\Column(name="buyer_fee", type="float", options={"unsigned":"true"})
     */
    private $buyerFee = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="invite_token", type="string", length=32)
     */
    private $inviteToken;

    /**
     * @var bool
     *
     * @ORM\Column(name="hide_fee", type="boolean")
     */
    private $hideFee = false;

    /**
     * @return User|null
     */
    public function getOwner(): ?User
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     *
     * @return Room
     */
    public function setOwner(User $owner): Room
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function isOwner(User $user): bool
    {
        return $this->owner === $user;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Room
     */
    public function setName(string $name): Room
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSphere(): ?string
    {
        return $this->sphere;
    }

    /**
     * @param string $sphere
     *
     * @return Room
     */
    public function setSphere(string $sphere): Room
    {
        $this->sphere = $sphere;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLeadCriteria(): ?string
    {
        return $this->leadCriteria;
    }

    /**
     * @param null|string $leadCriteria
     *
     * @return Room
     */
    public function setLeadCriteria(?string $leadCriteria): Room
    {
        $this->leadCriteria = $leadCriteria;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLeadPrice(): ?int
    {
        return $this->leadPrice;
    }

    /**
     * @param int|null $leadPrice
     *
     * @return Room
     */
    public function setLeadPrice(?int $leadPrice): Room
    {
        $this->leadPrice = $leadPrice;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isPlatformWarranty(): ?bool
    {
        return $this->platformWarranty;
    }

    /**
     * @param bool|null $platformWarranty
     *
     * @return Room
     */
    public function setPlatformWarranty(?bool $platformWarranty = false): Room
    {
        $this->platformWarranty = $platformWarranty;

        return $this;
    }

    /**
     * @param float|null $buyerFee
     *
     * @return Room
     */
    public function setBuyerFee(?float $buyerFee): self
    {
        $this->buyerFee = $buyerFee;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getBuyerFee(): ?float
    {
        return $this->buyerFee;
    }


    /**
     * @param string $inviteToken
     *
     * @return Room
     */
    public function setInviteToken(string $inviteToken): self
    {
        $this->inviteToken = $inviteToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getInviteToken(): string
    {
        return $this->inviteToken;
    }

    /**
     * @param bool $value
     *
     * @return Room
     */
    public function setHideFee(bool $value): self
    {
        $this->hideFee = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHideFee(): bool
    {
        return $this->hideFee;
    }
}