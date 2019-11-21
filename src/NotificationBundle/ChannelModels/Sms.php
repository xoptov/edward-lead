<?php

namespace NotificationBundle\ChannelModels;

use Symfony\Component\Validator\Constraints as Assert;

class Sms implements ChannelInterface
{
    /**
     * @Assert\NotBlank
     * @var string
     */
    private $phone;

    /**
     * @Assert\NotBlank
     * @var string
     */
    private $from;

    /**
     * @Assert\NotBlank
     * @var string
     */
    private $body;

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @param string $from
     */
    public function setFrom(string $from): void
    {
        $this->from = $from;
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
}