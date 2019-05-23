<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\HistoryActionRepository")
 * @ORM\Table(name="history_action")
 * @ORM\HasLifecycleCallbacks
 */
class HistoryAction
{
    const ACTION_LOGIN = 'login';

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"="true"})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string")
     */
    private $action;

    /**
     * @var array
     *
     * @ORM\Column(name="subject", type="array", nullable=true)
     */
    private $subject;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="happened_at", type="datetime")
     */
    private $happenedAt;

    /**
     * @param string $action
     */
    public function __construct(?string $action = self::ACTION_LOGIN)
    {
        $this->action = $action;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param User $user
     *
     * @return HistoryAction
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
     * @param string $action
     *
     * @return HistoryAction
     */
    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param array $subject
     *
     * @return HistoryAction
     */
    public function setSubject(array $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getSubject(): ?array
    {
        return $this->subject;
    }

    /**
     * @return \DateTime
     */
    public function getHappenedAt(): \DateTime
    {
        return $this->happenedAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist(): void
    {
        $this->happenedAt = new \DateTime();
    }
}