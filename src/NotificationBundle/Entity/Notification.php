<?php

namespace NotificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NotificationBundle\ChannelModels\ChannelInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Notification
 *
 * @ORM\Table(name="notification")
 * @ORM\Entity(repositoryClass="NotificationBundle\Repository\NotificationRepository")
 */
class Notification implements ChannelInterface
{

    const READ_STATUS_NEW = 'NEW';
    const READ_STATUS_VIEWED = 'VIEWED';

    const TYPE_DEFAULT = 'DEFAULT';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\NotBlank
     *
     * @var UserNotificationInterface
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="notifications")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @Assert\NotBlank
     *
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=255)
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column(name="read_status", type="string", length=10)
     */
    private $readStatus = self::READ_STATUS_NEW;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=10)
     */
    private $type = self::TYPE_DEFAULT;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set user.
     *
     * @param UserNotificationInterface $user
     *
     * @return Notification
     */
    public function setUser(UserNotificationInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return UserNotificationInterface
     */
    public function getUser(): UserNotificationInterface
    {
        return $this->user;
    }

    /**
     * Set message.
     *
     * @param string $message
     *
     * @return Notification
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message.
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Set readStatus.
     *
     * @param string $readStatus
     *
     * @return Notification
     */
    public function setReadStatus(string $readStatus): self
    {
        $this->readStatus = $readStatus;

        return $this;
    }

    /**
     * Get readStatus.
     *
     * @return string
     */
    public function getReadStatus(): string
    {
        return $this->readStatus;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return Notification
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
