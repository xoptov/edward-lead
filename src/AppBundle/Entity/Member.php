<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Part\CreatedAtTrait;
use AppBundle\Entity\Part\IdentificatorTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="member")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MemberRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Member implements IdentifiableInterface
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
     * @param int $id
     *
     * @return Member
     */
    public function setId(int $id): Member
    {
        $this->id = $id;
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
     * @return User|null
     */
    public function getUser(): ?User
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

    /**
     * @return bool
     */
    public function isCompany(): bool
    {
        return $this->user->isCompany();
    }

    /**
     * @return bool
     */
    public function isWebmaster(): bool
    {
        return $this->user->isWebmaster();
    }
}