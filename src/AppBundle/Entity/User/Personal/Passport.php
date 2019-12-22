<?php

namespace AppBundle\Entity\User\Personal;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Passport
{
    /**
     * @var string|null
     */
    private $serialNumber;

    /**
     * @var string|null
     */
    private $issuer;

    /**
     * @var DateTime|null
     */
    private $issueDate;

    /**
     * @var string|null
     */
    private $permanentAddress;

    /**
     * @return string|null
     */
    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    /**
     * @param string|null $serialNumber
     * 
     * @return Passport
     */
    public function setSerialNumber(?string $serialNumber): self
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIssuer(): ?string
    {
        return $this->issuer;
    }

    /**
     * @param string|null $issuer
     * 
     * @return Passport
     */
    public function setIssuer(?string $issuer): self
    {
        $this->issuer = $issuer;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getIssueDate(): ?DateTime
    {
        if ($this->issueDate) {
            return clone $this->issueDate;
        }

        return $this->issueDate;
    }

    /**
     * @param DateTime $issueDate
     * 
     * @return Passport
     */
    public function setIssueDate(DateTime $issueDate): self
    {
        $this->issueDate = $issueDate;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPermanentAddress(): ?string
    {
        return $this->permanentAddress;
    }

    /**
     * @param string|null $permanentAddress
     * 
     * @return Passport
     */
    public function setPermanentAddress(?string $permanentAddress): self
    {
        $this->permanentAddress = $permanentAddress;

        return $this;
    }
}
