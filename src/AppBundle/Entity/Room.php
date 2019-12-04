<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Part\EnabledTrait;
use AppBundle\Entity\Part\IdentificatorTrait;
use AppBundle\Entity\Part\TimeTrackableTrait;
use AppBundle\Entity\Room\Schedule;
use Doctrine\ORM\Mapping as ORM;
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
     * @Assert\Length(
     *     max=1000,
     *     maxMessage="Максимальное количество символов 1000"
     * )
     *
     * @ORM\Column(name="lead_criteria", type="text", nullable=true)
     */
    private $leadCriteria;

    /**
     * @var int|null
     *
     * @Assert\Range(
     *     min="100",
     *     minMessage="Минимальная стоимость 1",
     *     max="999999",
     *     maxMessage="Максимальная стоимость 9999.99"
     * )
     *
     * @ORM\Column(name="lead_price", type="integer", nullable=true, options={"unsigned":"true"})
     */
    private $leadPrice;

    /**
     * @var bool
     *
     * @Assert\IsTrue(
     *     message="Гарантия должна быть включена если включен таймер",
     *     groups={"timer"}
     * )
     *
     * @ORM\Column(name="platform_warranty", type="boolean")
     */
    private $platformWarranty = false;

    /**
     * @var float|null
     *
     * @ORM\Column(name="buyer_fee", type="float", nullable=true, options={"unsigned":"true"})
     */
    private $buyerFee;

    /**
     * @var int
     *
     * @Assert\GreaterThan(
     *     value=0,
     *     message="Значение должно быть положительным или пустым"
     * )
     *
     * @ORM\Column(name="hidden_margin", type="integer", nullable=true, options={"unsigned":"true"})
     */
    private $hiddenMargin;

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
     * @var bool
     *
     * @ORM\Column(name="timer", type="boolean")
     */
    private $timer = false;

    /**
     * @var City|null
     *
     * @Assert\NotBlank(
     *     message="Для таймера необходимо указать город",
     *     groups={"timer"}
     * )
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    private $city;

    /**
     * @var Schedule|null
     *
     * @Assert\Valid(groups={"timer"})
     *
     * @ORM\Embedded(class="AppBundle\Entity\Room\Schedule")
     */
    private $schedule;

    /**
     * @var int|null
     *
     * @Assert\NotBlank(
     *     message="Необходимо указать рабочии часы комнаты",
     *     groups={"timer"}
     * )
     * @Assert\Range(
     *     min=1,
     *     max=24,
     *     minMessage="Минимальное кол-во {{ limit }} часов на обработку",
     *     maxMessage="Максимальное кол-во {{ limit }} часов на обработку",
     *     groups={"timer"}
     * )
     *
     * @ORM\Column(name="execution_hours", type="smallint", nullable=true, options={"unsigned":true})
     */
    private $executionHours;

    /**
     * @var int|null
     *
     * @Assert\NotBlank(
     *     message="Необходимо указать количество лидов",
     *     groups={"timer"}
     * )
     * @Assert\Range(
     *     min=1,
     *     max=1000,
     *     minMessage="Минимальное кол-во {{ limit }} лидов в день по таймеру",
     *     maxMessage="Максимальное кол-во {{ limit }} лидов в день по таймеру",
     *     groups={"timer"}
     * )
     *
     * @ORM\Column(name="leads_per_day", type="smallint", nullable=true, options={"unsigned":true})
     */
    private $leadsPerDay;

    /**
     * @param int $id
     *
     * @return Room
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

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
     * @param int|null $divisor
     *
     * @return int|null
     */
    public function getLeadPrice(?int $divisor = null): ?int
    {
        if ($divisor && $this->leadPrice) {
            return $this->leadPrice / $divisor;
        }

        return $this->leadPrice;
    }

    /**
     * @return bool
     */
    public function hasLeadPrice(): bool
    {
        return !empty($this->leadPrice);
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
     * @param int|null $value
     *
     * @return Room
     */
    public function setHiddenMargin(?int $value): self
    {
        $this->hiddenMargin = $value;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getHiddenMargin(): ?int
    {
        return $this->hiddenMargin;
    }

    /**
     * @return bool
     */
    public function hasHiddenMargin(): bool
    {
        return !empty($this->hiddenMargin);
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

    /**
     * @param bool $value
     *
     * @return Room
     */
    public function setTimer(bool $value): self
    {
        $this->timer = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isTimer(): bool
    {
        return $this->timer;
    }

    /**
     * @param City|null $city
     *
     * @return Room
     */
    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return City|null
     */
    public function getCity(): ?City
    {
        return $this->city;
    }

    /**
     * @param Schedule $schedule
     *
     * @return Room
     */
    public function setSchedule(Schedule $schedule): self
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * @return Schedule|null
     */
    public function getSchedule(): ?Schedule
    {
        return $this->schedule;
    }

    /**
     * @param int $hours
     *
     * @return Room
     */
    public function setExecutionHours(int $hours): self
    {
        $this->executionHours = $hours;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getExecutionHours(): ?int
    {
        return $this->executionHours;
    }

    /**
     * @param int $limit
     *
     * @return Room
     */
    public function setLeadsPerDay(int $limit): self
    {
        $this->leadsPerDay = $limit;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLeadsPerDay(): ?int
    {
        return $this->leadsPerDay;
    }
}
