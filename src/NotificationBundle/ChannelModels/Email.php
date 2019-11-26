<?php

namespace NotificationBundle\ChannelModels;

use Symfony\Component\Validator\Constraints as Assert;

class Email implements ChannelInterface
{
    /**
     * @Assert\NotBlank
     * @Assert\Email
     * @var string
     */
    private $toEmail;

    /**
     * @Assert\NotBlank
     * @var string
     */
    private $templateId;


    /**
     * @var array
     */
    private $params = [];

    /**
     * @return string
     */
    public function getToEmail(): string
    {
        return $this->toEmail;
    }

    /**
     * @param string $toEmail
     */
    public function setToEmail(string $toEmail): void
    {
        $this->toEmail = $toEmail;
    }

    /**
     * @return string
     */
    public function getTemplateId(): string
    {
        return $this->templateId;
    }

    /**
     * @param string $templateId
     */
    public function setTemplateId(string $templateId): void
    {
        $this->templateId = $templateId;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }


}