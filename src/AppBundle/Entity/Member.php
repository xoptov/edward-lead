<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Part\CreatedAtTrait;
use AppBundle\Entity\Part\IdentificatorTrait;

/**
 * @ORM\Table(name="member")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Member
{
    use IdentificatorTrait;

    use CreatedAtTrait;

    /**
     * @var Room
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Room")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id", nullable=false)
     */
    private $room;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @return Room
     */
    public function getRoom(): Room
    {
        return $this->room;
    }

    /**
     * @param Room $room
     *
     * @return Member
     */
    public function setRoom(Room $room): Member
    {
        $this->room = $room;

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
     * @param User $user
     *
     * @return Member
     */
    public function setUser(User $user): Member
    {
        $this->user = $user;

        return $this;
    }
}