<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Part\IdentificatorTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @UniqueEntity(
 *     fields={"room", "property"},
 *     message="Не уникальные каналы поступления в пределах комнаты"
 * )
 */
class RoomChannel
{
    use IdentificatorTrait;

    /**
     * @var Room
     *
     * @Assert\NotBlank(message="Необходимо указать комнату")
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Room", inversedBy="channels")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $room;

    /**
     * @var Property
     *
     * @Assert\NotBlank(message="Необходимо указать канал поступления лидов")
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Property")
     * @ORM\JoinColumn(name="property_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $property;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $allowed = false;

    /**
     * @param Room $room
     *
     * @return RoomChannel
     */
    public function setRoom(Room $room): self
    {
        $this->room = $room;

        return $this;
    }

    /**
     * @return Room
     */
    public function getRoom(): ?Room
    {
        return $this->room;
    }

    /**
     * @param Property $property
     *
     * @return RoomChannel
     */
    public function setProperty(Property $property): self
    {
        $this->property = $property;

        return $this;
    }

    /**
     * @return Property
     */
    public function getProperty(): ?Property
    {
        return $this->property;
    }

    /**
     * @return string|null
     */
    public function getPropertyValue(): ?string
    {
        if ($this->getProperty()) {
            return $this->getProperty()->getValue();
        }

        return null;
    }

    /**
     * @param bool $allowed
     *
     * @return RoomChannel
     */
    public function setAllowed(bool $allowed): self
    {
        $this->allowed = $allowed;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAllowed(): bool
    {
        return $this->allowed;
    }
}