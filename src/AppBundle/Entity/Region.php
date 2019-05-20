<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="region")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Region
{
    use TimeTrackableTrait;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="smallint", options={"unsigned"="true"})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Country|null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     */
    private $country;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=30, unique=true)
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled = false;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param Country $country
     *
     * @return Region
     */
    public function setCountry(Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return Country|null
     */
    public function getCountry(): ?Country
    {
        return $this->country;
    }

    /**
     * @param string|null $name
     *
     * @return Region
     */
    public function setName(string $name): self
    {
        $this->name = $name;

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
     * @param boolean $enabled
     *
     * @return Region
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
