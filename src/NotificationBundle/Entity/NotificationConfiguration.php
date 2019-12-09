<?php

namespace NotificationBundle\Entity;

use AppBundle\Entity\Part\TimeTrackableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Notification
 *
 * @ORM\Table(name="notification_configuration")
 * @ORM\Entity(repositoryClass="NotificationBundle\Repository\NotificationConfigurationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class NotificationConfiguration
{
    use TimeTrackableTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @NotBlank
     *
     * @var UserNotificationInterface
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="notifications")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @NotBlank
     *
     * @var string
     *
     * @ORM\Column(name="case", type="string", length=255)
     */
    private $case;

    /**
     * @NotBlank
     *
     * @var string
     *
     * @ORM\Column(name="channel", type="string", length=255)
     */
    private $channel;

    /**
     * @NotBlank
     *
     * @var bool
     *
     * @ORM\Column(name="disabled", type="boolean")
     */
    private $disabled;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return UserNotificationInterface
     */
    public function getUser(): UserNotificationInterface
    {
        return $this->user;
    }

    /**
     * @param UserNotificationInterface $user
     */
    public function setUser(UserNotificationInterface $user): void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getCase(): string
    {
        return $this->case;
    }

    /**
     * @param string $case
     */
    public function setCase(string $case): void
    {
        $this->case = $case;
    }

    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channel;
    }

    /**
     * @param string $channel
     */
    public function setChannel(string $channel): void
    {
        $this->channel = $channel;
    }

    /**
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * @param bool $disabled
     */
    public function setDisabled(bool $disabled): void
    {
        $this->disabled = $disabled;
    }
}
