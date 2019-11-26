<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Part\CreatedAtTrait;
use AppBundle\Entity\Part\IdentificatorTrait;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AuthenticationFailureRepository")
 * @ORM\HasLifecycleCallbacks
 */
class AuthenticationFailure
{
    use IdentificatorTrait;

    use CreatedAtTrait;

    /**
     * @var int
     * 
     * @ORM\Column(type="integer", options={"unsigned":"true"})
     */
    private $ipAddress;

    /**
     * @param int $ipAddress
     */
    public function __construct(int $ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * @return int
     */
    public function getIpAddress(): int
    {
        return $this->ipAddress;
    }
}
