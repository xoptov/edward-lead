<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ClientAccount extends Account
{
    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User", inversedBy="account")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", unique=true, nullable=true, onDelete="CASCADE")
     */
    private $user;

    /**
     * @param User $user
     * @return ClientAccount
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