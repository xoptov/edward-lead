<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Part\EnabledTrait;
use AppBundle\Entity\Part\TimeTrackableTrait;
use AppBundle\Entity\Part\IdentificatorTrait;

/**
 * @ORM\Table(name="city")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class City
{
    use IdentificatorTrait;

    use TimeTrackableTrait;

    use EnabledTrait;

    /**
     * @var Region
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Region")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id", nullable=false)
     */
    private $region;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=30)
     */
    private $name;

    /**
     * @var int|null
     *
     * @ORM\Column(name="lead_price", type="integer", nullable=true, options={"unsigned":true})
     */
    private $leadPrice;

    /**
     * @var int|null
     *
     * @ORM\Column(name="star_price", type="integer", nullable=true, options={"unsigned":true})
     */
    private $starPrice;

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
}

