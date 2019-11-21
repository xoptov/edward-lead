<?php

namespace NotificationBundle\ChannelModels;

use Symfony\Component\Validator\Constraints as Assert;

class WebPush implements ChannelInterface
{
    /**
     * @Assert\NotBlank
     * @var string
     */
    private $title;

    /**
     * @Assert\NotBlank
     * @var string
     */
    private $body;

    /**
     * @Assert\NotBlank
     * @var string
     */
    private $link;

    /**
     * @Assert\NotBlank
     * @var string
     */
    private $pushToken;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    /**
     * @return string
     */
    public function getPushToken(): string
    {
        return $this->pushToken;
    }

    /**
     * @param string $pushToken
     */
    public function setPushToken(string $pushToken): void
    {
        $this->pushToken = $pushToken;
    }


}