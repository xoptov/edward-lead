<?php

namespace NotificationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

trait UserNotificationTrait
{
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="NotificationBundle\Entity\Notification", mappedBy="user")
     */
    private $notifications;

    /**
     * @var string
     *
     * @ORM\Column(name="telegram_chat_id", type="string", length=255, nullable=true)
     */
    private $telegramChatId;

    /**
     * @var string
     *
     * @ORM\Column(name="telegram_auth_token", type="string", length=255, nullable=true)
     */
    private $telegramAuthToken;

    /**
     * @var string|null
     *
     * @ORM\Column(name="web_push_token", type="string", length=255, nullable=true)
     */
    private $webPushToken;

    /**
     * @return mixed
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * @param ArrayCollection $notifications
     */
    public function setNotifications(ArrayCollection $notifications): void
    {
        $this->notifications = $notifications;
    }

    /**
     * @return string
     */
    public function getTelegramChatId(): string
    {
        return $this->telegramChatId;
    }

    /**
     * @param string $telegramChatId
     */
    public function setTelegramChatId(string $telegramChatId): void
    {
        $this->telegramChatId = $telegramChatId;
    }

    /**
     * @return string
     */
    public function getTelegramAuthToken(): string
    {
        return $this->telegramAuthToken;
    }

    /**
     * @param string $telegramAuthToken
     */
    public function setTelegramAuthToken(string $telegramAuthToken): void
    {
        $this->telegramAuthToken = $telegramAuthToken;
    }

    /**
     * @return string|null
     */
    public function getWebPushToken(): ?string
    {
        return $this->webPushToken;
    }

    /**
     * @param string $webPushToken
     */
    public function setWebPushToken(string $webPushToken): void
    {
        $this->webPushToken = $webPushToken;
    }
}
