<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="lead")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Lead
{
    use TimeTrackableTrait;

    const STATUS_NEW = 'new';

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"="true"})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=12)
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
     * @ORM\Column(name="name", type="string", length=30, nullable=true)
     */
    private $name;

    /**
     * @var City
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id", nullable=false)
     */
    private $city;

    /**
     * @var Property|null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Property")
     * @ORM\JoinColumn(name="advertising_channel_id", referencedColumnName="id")
     */
    private $advertisingChannel;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="order_date", type="date", nullable=true)
     */
    private $orderDate;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="decision_maker", type="boolean", nullable=true)
     */
    private $decisionMaker;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="made_measurement", type="boolean", nullable=true)
     */
    private $madeMeasurement;

    /**
     * @var int|null
     *
     * @ORM\Column(name="interest_assessment", type="smallint", nullable=true)
     */
    private $interestAssessment;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expiration_date", type="datetime")
     */
    private $expirationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string")
     */
    private $status = self::STATUS_NEW;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer", options={"unsigned":true})
     */
    private $price;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return string
     */
    public function getPhone(): string
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
     * @param City $city
     *
     * @return Lead
     */
    public function setCity(City $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return City
     */
    public function getCity(): City
    {
        return $this->city;
    }

    /**
     * @param Property|null $advertisingChannel
     *
     * @return Lead
     */
    public function setAdvertisingChannel(?Property $advertisingChannel): self
    {
        $this->advertisingChannel = $advertisingChannel;

        return $this;
    }

    /**
     * @return Property|null
     */
    public function getAdvertisingChannel(): ?Property
    {
        return $this->advertisingChannel;
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
     * @param bool|null $decisionMaker
     *
     * @return Lead
     */
    public function setDecisionMaker(?bool $decisionMaker): self
    {
        $this->decisionMaker = $decisionMaker;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isDecisionMaker(): ?bool
    {
        return $this->decisionMaker;
    }

    /**
     * @param bool|null $madeMeasurement
     *
     * @return Lead
     */
    public function setMadeMeasurement(?bool $madeMeasurement): self
    {
        $this->madeMeasurement = $madeMeasurement;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isMadeMeasurement(): ?bool
    {
        return $this->madeMeasurement;
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
     * @param \DateTime $expirationDate
     *
     * @return Lead
     */
    public function setExpirationDate(\DateTime $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExpirationDate(): \DateTime
    {
        return $this->expirationDate;
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
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }
}