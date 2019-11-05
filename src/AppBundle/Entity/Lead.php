<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Lead\Timer;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Part\TimeTrackableTrait;
use AppBundle\Entity\Part\IdentificatorTrait;
use AppBundle\Validator\Constraints\UniqueLead;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="lead")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LeadRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueLead
 */
class Lead implements IdentifiableInterface
{
    use IdentificatorTrait;

    use TimeTrackableTrait;

    const STATUS_EXPECT      = 'expect';      // "ожидает", ранее "active"
    const STATUS_IN_WORK     = 'in_work';     // "в работе", ранее "reserved"
    const STATUS_TARGET      = 'target';      // "целевой", ранее "sold"
    const STATUS_ARBITRATION = 'arbitration'; // "арбитраж", ранее "no_target"
    const STATUS_NOT_TARGET  = 'not_target';  // "не целевой", ранее "blocked"
    const STATUS_ARCHIVE     = 'archive';     // "архив", ранее "expired"

    const DECISION_MAKER_UNKNOWN = 0;
    const DECISION_MAKER_YES     = 1;
    const DECISION_MAKER_NO      = 2;

    /**
     * @var Room|null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Room")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id")
     */
    private $room;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Необходимо указать номер телефона")
     * @Assert\Regex(
     *     pattern="/^7\d{10}$/",
     *     message="Невалидный формат телефона"
     * )
     *
     * @ORM\Column(name="phone", type="string", length=11)
     */
    private $phone;

    /**
     * @var Trade
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Trade", mappedBy="lead")
     */
    private $trade;

    /**
     * @var string|null
     *
     * @Assert\Length(max=40, maxMessage="Значение в поле 'Имя Лида' должно быть не более {{ limit }} символов")
     *
     * @ORM\Column(name="name", type="string", length=40, nullable=true)
     */
    private $name;

    /**
     * @var City|null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    private $city;

    /**
     * @var Property|null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Property")
     * @ORM\JoinColumn(name="channel_id", referencedColumnName="id")
     */
    private $channel;

    /**
     * @var \DateTime|null
     *
     * @Assert\LessThanOrEqual(
     *     value="+1 day",
     *     message="Дата не может быть в будущем"
     * )
     *
     * @ORM\Column(name="order_date", type="date", nullable=true)
     */
    private $orderDate;

    /**
     * @var int|null
     *
     * @ORM\Column(name="decision_maker", type="smallint", nullable=true)
     */
    private $decisionMaker;

    /**
     * @var int|null
     *
     * @ORM\Column(name="interest_assessment", type="smallint", nullable=true)
     */
    private $interestAssessment;

    /**
     * @var string|null
     *
     * @Assert\Length(max=2000, maxMessage="Описание не должно привышать {{ limit }} символов")
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(name="audio_record", type="string", nullable=true)
     */
    private $audioRecord;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string")
     */
    private $status = self::STATUS_EXPECT;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer", options={"unsigned":true})
     */
    private $price;

    /**
     * @var Timer|null
     *
     * @ORM\Embedded(class="AppBundle\Entity\Lead\Timer")
     */
    private $timer;

    /**
     * @param Room|null $room
     *
     * @return Lead
     */
    public function setRoom(?Room $room): self
    {
        $this->room = $room;

        return $this;
    }

    /**
     * @return Room|null
     */
    public function getRoom(): ?Room
    {
        return $this->room;
    }

    /**
     * @return bool
     */
    public function hasRoom(): bool
    {
        return $this->room instanceof Room;
    }

    /**
     * @return bool
     */
    public function isPlatformWarranty(): bool
    {
        if ($this->room) {
            return $this->room->isPlatformWarranty();
        }

        return true;
    }

    /**
     * @param User $user
     *
     * @return Lead
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param string $phone
     *
     * @return Lead
     */
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param Trade $trade
     *
     * @return Lead
     */
    public function setTrade(Trade $trade): self
    {
        $this->trade = $trade;

        return $this;
    }

    /**
     * @return Trade|null
     */
    public function getTrade(): ?Trade
    {
        return $this->trade;
    }

    /**
     * @return bool
     */
    public function hasTrade(): bool
    {
        return !empty($this->trade);
    }

    /**
     * @return null|User
     */
    public function getBuyer(): ?User
    {
        if ($this->trade) {
            return $this->trade->getBuyer();
        }

        return null;
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
     * @param null|string $name
     *
     * @return Lead
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param City|null $city
     *
     * @return Lead
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
     * @return null|string
     */
    public function getCityName(): ?string
    {
        if ($this->city) {
            return $this->city->getName();
        }

        return null;
    }

    /**
     * @param Property|null $channel
     *
     * @return Lead
     */
    public function setChannel(?Property $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * @return Property|null
     */
    public function getChannel(): ?Property
    {
        return $this->channel;
    }

    /**
     * @return int|null
     */
    public function getChannelId(): ?int
    {
        if ($this->channel) {
            return $this->channel->getId();
        }

        return null;
    }

    /**
     * @return null|string
     */
    public function getChannelName(): ?string
    {
        if ($this->channel) {
            return $this->channel->getValue();
        }

        return null;
    }

    /**
     * @param \DateTime|null $orderDate
     *
     * @return Lead
     */
    public function setOrderDate(?\DateTime $orderDate): self
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getOrderDate(): ?\DateTime
    {
        return $this->orderDate;
    }

    /**
     * @param string $format
     *
     * @return string
     */
    public function getOrderDateFormatted(string $format): ?string
    {
        if ($this->orderDate) {
            return $this->orderDate->format($format);
        }

        return null;
    }

    /**
     * @param int|null $decisionMaker
     *
     * @return Lead
     */
    public function setDecisionMaker(?int $decisionMaker): self
    {
        $this->decisionMaker = $decisionMaker;

        return $this;
    }

    /**
     * @return int|null
     */
    public function isDecisionMaker(): ?int
    {
        return $this->decisionMaker;
    }

    /**
     * @param int|null $interestAssessment
     *
     * @return Lead
     */
    public function setInterestAssessment(?int $interestAssessment): self
    {
        $this->interestAssessment = $interestAssessment;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getInterestAssessment(): ?int
    {
        return $this->interestAssessment;
    }

    /**
     * @param null|string $description
     *
     * @return Lead
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return string|null
     */
    public function getAudioRecord(): ?string
    {
        return $this->audioRecord;
    }

    /**
     * @param string $audioRecord
     *
     * @return Lead
     */
    public function setAudioRecord(string $audioRecord): self
    {
        $this->audioRecord = $audioRecord;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasAudioRecord(): bool
    {
        return !empty($this->audioRecord);
    }

    /**
     * @param string $status
     *
     * @return Lead
     */
    public function setStatus(string $status): Lead
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param int $price
     *
     * @return Lead
     */
    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @param int|null $divisor
     *
     * @return int|null
     */
    public function getPrice(?int $divisor = null): ?int
    {
        if ($divisor) {
            return $this->price / $divisor;
        }

        return $this->price;
    }

    /**
     * @param Timer $timer
     *
     * @return Lead
     */
    public function setTimer(Timer $timer): self
    {
        $this->timer = $timer;

        return $this;
    }

    /**
     * @return Timer|null
     */
    public function getTimer(): ?Timer
    {
        return $this->timer;
    }

    /**
     * @return bool
     */
    public function hasTimer(): bool
    {
        return !empty($this->timer);
    }

    /**
     * @return \DateTime|null
     */
    public function getTimerEndAt(): ?\DateTime
    {
        if ($this->timer) {
            return $this->timer->getEndAt();
        }

        return null;
    }

    /**
     * @return int|null
     */
    public function getHiddenMargin(): ?int
    {
        if ($this->room && $this->room->hasHiddenMargin()) {
            return $this->room->getHiddenMargin();
        }

        return null;
    }

    /**
     * @param int|null $divisor
     *
     * @return int|null
     */
    public function getPriceWithMargin(?int $divisor = null): ?int
    {
        $hiddenMargin = $this->getHiddenMargin();

        if ($hiddenMargin) {
            $price = $this->price + $hiddenMargin;
        } else {
            $price = $this->price;
        }

        if ($divisor) {
            return $price / $divisor;
        }

        return $price;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function isOwner(User $user): bool
    {
        return $this->user === $user;
    }

    /**
     * @return bool
     */
    public function isExpected(): bool
    {
        return self::STATUS_EXPECT === $this->status;
    }
}