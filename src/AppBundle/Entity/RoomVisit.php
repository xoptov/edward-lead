<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Part\IdentificatorTrait;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class RoomVisit
{
    use IdentificatorTrait;

    /**
     * @var Room
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Room")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $room;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $visitedAt;

    /**
     * @param Room $room
     * @param User $user
     */
    public function __construct(Room $room, User $user)
    {
        $this->room = $room;
        $this->user = $user;
    }

    /**
     * @return Room
     */
    public function getRoom(): Room
    {
        return $this->room;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return \DateTime
     */
    public function getVisitedAt(): \DateTime
    {
        return $this->visitedAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->visitedAt = new \DateTime();
    }
}
