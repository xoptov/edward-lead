<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Part\EnabledTrait;
use AppBundle\Entity\Part\TimeTrackableTrait;
use AppBundle\Entity\Part\IdentificatorTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="city")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class City implements IdentifiableInterface
{
    use IdentificatorTrait;

    use TimeTrackableTrait;

    use EnabledTrait;

    /**
     * @var Region
     *
     * @Assert\Valid
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Region")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id", nullable=false)
     */
    private $region;

    /**
     * @var string
     *
     * @Assert\Length(max=30, maxMessage="Название может содержать только {{ limit }} символов")
     *
     * @ORM\Column(name="name", type="string", length=30)
     */
    private $name;

    /**
     * @var int|null
     *
     * @Assert\Range(min="100", minMessage="Минимальное значение 1", max="999900", maxMessage="Максимальное значение 9999")
     *
     * @ORM\Column(name="lead_price", type="integer", nullable=true, options={"unsigned":true})
     */
    private $leadPrice;

    /**
     * @var int|null
     *
     * @Assert\Range(min="100", minMessage="Минимальное значение 1", max="999900", maxMessage="Максимальное значение 9999")
     *
     * @ORM\Column(name="star_price", type="integer", nullable=true, options={"unsigned":true})
     */
    private $starPrice;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(message="Необходимо указать временую зону")
     *
     * @ORM\Column(name="timezone", type="string", nullable=true)
     */
    private $timezone;

    /**
     * @param Region $region
     *
     * @return City
     */
    public function setRegion(Region $region): self
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @return Region
     */
    public function getRegion(): ?Region
    {
        return $this->region;
    }

    /**
     * @return bool
     */
    public function hasRegion(): bool
    {
        return $this->region instanceof Region;
    }

    /**
     * @return int|null
     */
    public function getRegionId(): ?int
    {
        if ($this->region) {
            return $this->region->getId();
        }

        return null;
    }

    /**
     * @return null|string
     */
    public function getRegionName(): ?string
    {
        if ($this->region) {
            return $this->region->getName();
        }

        return null;
    }

    /**
     * @param string $name
     *
     * @return City
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param integer|null $leadPrice
     *
     * @return City
     */
    public function setLeadPrice(?int $leadPrice = null): self
    {
        $this->leadPrice = $leadPrice;

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
     * @param integer|null $starPrice
     *
     * @return City
     */
    public function setStarPrice(?int $starPrice = null): self
    {
        $this->starPrice = $starPrice;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getStarPrice(): ?int
    {
        return $this->starPrice;
    }

    /**
     * @param string $timezone
     *
     * @return City
     */
    public function setTimezone(string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTimezone(): ?string
    {
        return $this->timezone;
    }
}

