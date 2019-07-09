<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Part\EnabledTrait;
use AppBundle\Entity\Part\TimeTrackableTrait;
use AppBundle\Entity\Part\IdentificatorTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="room",options={"auto_increment"="1000"})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Room
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
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(message="Сфера должна быть указана")
     * @ORM\Column(name="sphere", type="string")
     */
    private $sphere;

    /**
     * @var string|null
     *
     * @ORM\Column(name="lead_criteria", type="text", nullable=true)
     */
    private $leadCriteria;

    /**
     * @var int|null
     *
     * @ORM\Column(name="lead_price", type="integer", nullable=true)
     */
    private $leadPrice;

    /**
     * @var bool
     *
     * @ORM\Column(name="platform_warranty", type="boolean")
     */
    private $platformWarranty = false;

    /**
     * @var string
     *
     * @ORM\Column(name="invite_token", type="string", length=32)
     */
    private $inviteToken;

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
     * @return bool
     */
    public function isPlatformWarranty(): bool
    {
        return $this->platformWarranty;
    }

    /**
     * @param bool $platformWarranty
     *
     * @return Room
     */
    public function setPlatformWarranty(bool $platformWarranty): Room
    {
        $this->platformWarranty = $platformWarranty;

        return $this;
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
}