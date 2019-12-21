<?php

namespace AppBundle\Entity\User;

use AppBundle\Entity\City;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
class Office
{
    /**
     * @var string|null
     *
     * @Assert\Length(
     *     max=30,
     *     maxMessage="Максимальная длинна названия офиса {{limit}} символов"
     * )
     *
     * @ORM\Column(
     *     type="string",
     *     length=30,
     *     nullable=true
     * )
     */
    private $name;

    /**
     * @var string|null
     *
     * @Assert\Regex(
     *     pattern="/^7\d{10}$/",
     *     message="Невалидный формат телефона"
     * )
     *
     * @ORM\Column(
     *     type="string",
     *     length=11,
     *     nullable=true
     * )
     */
    private $phone;

    /**
     * @var string|null
     *
     * @Assert\Length(
     *     max=150,
     *     maxMessage="Максимальная длинна адреса офиса {{limit}} символов"
     * )
     *
     * @ORM\Column(
     *     type="string",
     *     length=150,
     *     nullable=true
     * )
     */
    private $address;

    /**
     * @var ArrayCollection|City[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\City")
     */
    private $cities;

    public function __construct()
    {
        $this->cities = new ArrayCollection();
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     * @return Office
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param null|string $phone
     *
     * @return Office
     */
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param null|string $address
     *
     * @return Office
     */
    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return City[]|ArrayCollection
     */
    public function getCities()
    {
        return clone $this->cities;
    }

    /**
     * @param City $city
     *
     * @return bool
     */
    public function addCity(City $city): bool
    {
        return $this->cities->add($city);
    }

    /**
     * @param City $city
     *
     * @return bool
     */
    public function removeCity(City $city): bool
    {
        return $this->cities->removeElement($city);
    }
}
