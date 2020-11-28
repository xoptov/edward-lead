<?php

namespace AppBundle\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
class Organization
{
    /**
     * @var string|null
     *
     * @Assert\Length(
     *     max=50,
     *     maxMessage="Максимальная длинна короткого назнания {{limit}} символов"
     * )
     *
     * @ORM\Column(
     *     name="short_name",
     *     type="string",
     *     length=50,
     *     nullable=true
     * )
     */
    private $shortName;

    /**
     * @var string|null
     *
     * @Assert\Length(
     *     max=100,
     *     maxMessage="Максимальная длинна полного названия {{limit}} символов"
     * )
     *
     * @ORM\Column(
     *     name="large_name",
     *     type="string",
     *     length=100,
     *     nullable=true
     * )
     */
    private $largeName;

    /**
     * @var string|null
     *
     * @Assert\Length(
     *     min=10,
     *     minMessage="Минимальная длинна ИНН {{limit}} цифр",
     *     max=12,
     *     maxMessage="Максимальная длинна ИНН {{limit}} цифр"
     * )
     *
     * @ORM\Column(
     *     name="inn",
     *     type="string",
     *     length=12,
     *     nullable=true
     * )
     */
    private $inn;

    /**
     * @var string|null
     *
     * @Assert\Length(
     *     min=13,
     *     minMessage="Минимальная длинна ОГРН {{limit}} цифр",
     *     max=15,
     *     maxMessage="Максимальная длинна ОГРН {{limit}} цифр"
     * )
     *
     * @ORM\Column(
     *     name="ogrn",
     *     type="string",
     *     length=15,
     *     nullable=true
     * )
     */
    private $ogrn;

    /**
     * @var string|null
     *
     * @Assert\Length(
     *     min=9,
     *     minMessage="Минимальная длинна КПП {{limit}} цифр",
     *     max=9,
     *     maxMessage="Максимальная длинна КПП {{limit}} цифр"
     * )
     *
     * @ORM\Column(
     *     name="kpp",
     *     type="string",
     *     length=9,
     *     nullable=true
     * )
     */
    private $kpp;

    /**
     * @var string|null
     *
     * @Assert\Length(
     *     min=9,
     *     minMessage="Минимальная длинна БИК {{limit}} цифр",
     *     max=9,
     *     maxMessage="Максимальная длинна БИК {{limit}} цифр"
     * )
     *
     * @ORM\Column(
     *     name="bik",
     *     type="string",
     *     length=9,
     *     nullable=true
     * )
     */
    private $bik;

    /**
     * @var string|null
     *
     * @Assert\Length(
     *     min=20,
     *     minMessage="Минимальная длинна номера счёта {{limit}} цифр",
     *     max=25,
     *     maxMessage="Максимальная длинна номера счёта {{limit}} цифр"
     * )
     *
     * @ORM\Column(
     *     name="account_number",
     *     type="string",
     *     length=25,
     *     nullable=true
     * )
     */
    private $accountNumber;

    /**
     * @var string|null
     *
     * @Assert\Length(
     *     max=150,
     *     maxMessage="Максимальная длинна адреса {{limit}} символов"
     * )
     *
     * @ORM\Column(
     *     name="address",
     *     type="string",
     *     length=150,
     *     nullable=true
     * )
     */
    private $address;

    /**
     * @return null|string
     */
    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    /**
     * @param null|string $shortName
     *
     * @return Organization
     */
    public function setShortName(?string $shortName): self
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLargeName(): ?string
    {
        return $this->largeName;
    }

    /**
     * @param null|string $largeName
     *
     * @return Organization
     */
    public function setLargeName(?string $largeName): self
    {
        $this->largeName = $largeName;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getInn(): ?string
    {
        return $this->inn;
    }

    /**
     * @param null|string $inn
     *
     * @return Organization
     */
    public function setInn(?string $inn): self
    {
        $this->inn = $inn;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getOgrn(): ?string
    {
        return $this->ogrn;
    }

    /**
     * @param null|string $ogrn
     *
     * @return Organization
     */
    public function setOgrn(?string $ogrn): self
    {
        $this->ogrn = $ogrn;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getKpp(): ?string
    {
        return $this->kpp;
    }

    /**
     * @param null|string $kpp
     *
     * @return Organization
     */
    public function setKpp(?string $kpp): self
    {
        $this->kpp = $kpp;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getBik(): ?string
    {
        return $this->bik;
    }

    /**
     * @param null|string $bik
     *
     * @return Organization
     */
    public function setBik(?string $bik): self
    {
        $this->bik = $bik;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    /**
     * @param null|string $accountNumber
     *
     * @return Organization
     */
    public function setAccountNumber(?string $accountNumber): self
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param null|string $address
     *
     * @return Organization
     */
    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }
}