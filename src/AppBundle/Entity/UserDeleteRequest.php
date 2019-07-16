<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Part\IdentificatorTrait;
use AppBundle\Entity\Part\TimeTrackableTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_delete_request")
 * @ORM\HasLifecycleCallbacks
 */
class UserDeleteRequest implements IdentifiableInterface
{
    const STATUS_NEW = 'new';
    const STATUS_REJECTED = 'rejected';
    const STATUS_ACCEPTED = 'accepted';

    use IdentificatorTrait;

    use TimeTrackableTrait;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User", inversedBy="deleteRequest")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=8)
     */
    private $status = self::STATUS_NEW;

    /**
     * @param User $user
     *
     * @return UserDeleteRequest
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

    /**
     * @param string $status
     *
     * @return UserDeleteRequest
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return self::STATUS_NEW === $this->status;
    }

    /**
     * @return bool
     */
    public function isProcessed(): bool
    {
        return in_array($this->status, [self::STATUS_ACCEPTED, self::STATUS_REJECTED]);
    }
}