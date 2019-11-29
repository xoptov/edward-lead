<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Part\CreatedAtTrait;
use AppBundle\Entity\Part\IdentificatorTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class RoomJoinRequest
{
    use IdentificatorTrait, CreatedAtTrait;

    /**
     * @var Room
     * 
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="joinRequests")
     * @ORM\JoinColumn(
     *     name="room_id",
     *     referencedColumnName="id",
     *     nullable=false,
     *     onDelete="CASCADE"
     * )
     */
    private $room;

    /**
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(
     *     name="user_id",
     *     referencedColumnName="id",
     *     nullable=false,
     *     onDelete="CASCADE"
     * )
     */
    private $user;

    /**
     * @param Room $room
     * 
     * @return RoomJoinRequest
     */
    public function setRoom(Room $room): self
    {
        $this->room = $room;

        return $this;
    }

    /**
     * @return Room
     */
    public function getRoom(): Room
    {
        return $this->room;
    }

    /**
     * @param User $user
     * 
     * @return RoomJoinRequest
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
}