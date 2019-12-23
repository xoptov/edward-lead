<?php

namespace AppBundle\Entity\User\Personal;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
class Passport
{
    /**
     * @var string|null
     *
     * @Assert\Length(
     *     max=4,
     *     maxMessage="Максимальная длинна серии паспорта {{limit}} символов"
     * )
     *
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $serial;

    /**
     * @var string|null
     *
     * @Assert\Length(
     *     max=6,
     *     maxMessage="Максимальная длинна номера паспорта {{limit}} символов"
     * )
     *
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    private $number;

    /**
     * @var string|null
     *
     * @Assert\Length(
     *     max=150,
     *     maxMessage="Максимальная длинна наименования структоры выдавшей паспорт {{limit}} символов"
     * )
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $issuer;

    /**
     * @var DateTime|null
     *
     * @Assert\Date(message="Значение должно быть датой")
     *
     * @ORM\Column(name="issue_date", type="date", nullable=true)
     */
    private $issueDate;

    /**
     * @var string|null
     *
     * @Assert\Length(
     *     max=150,
     *     maxMessage="Максимальная длинна адреса {{limit}} символов"
     * )
     *
     * @ORM\Column(name="address", type="string", length=150, nullable=true)
     */
    private $address;

    /**
     * @return string|null
     */
    public function getSerial(): ?string
    {
        return $this->serial;
    }

    /**
     * @param string|null $serial
     * 
     * @return Passport
     */
    public function setSerial(?string $serial): self
    {
        $this->serial = $serial;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * @param string|null $number
     *
     * @return Passport
     */
    public function setNumber(?string $number): self
    {
        $this->number = $number;

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
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     * 
     * @return Passport
     */
    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }
}
