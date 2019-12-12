<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Part\CreatedAtTrait;
use AppBundle\Entity\Part\IdentificatorTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OfferRepository")
 * @ORM\HasLifecycleCallbacks
 */
class OfferRequest
{
    use IdentificatorTrait, CreatedAtTrait;

    /**
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="User", inversedBy="offerRequests")
     * @ORM\JoinColumn(
     *     name="user_id",
     *     referencedColumnName="id",
     *     nullable=false,
     *     onDelete="CASCADE"
     * )
     */
    private $user;

    /**
     * @param User $user
     * 
     * @return OfferRequest
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