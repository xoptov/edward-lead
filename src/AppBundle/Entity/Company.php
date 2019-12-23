<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Part\TimeTrackableTrait;
use AppBundle\Entity\Part\IdentificatorTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="company")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Company implements IdentifiableInterface
{
    use IdentificatorTrait;

    use TimeTrackableTrait;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User", inversedBy="company")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @var string|null
     *
     * @Assert\Length(max=50)
     * @Assert\NotBlank(
     *     message="Необходимо указать короткое название компании",
     *     groups={"Company"}
     * )
     *
     * @ORM\Column(name="short_name", type="string", length=50)
     */
    private $shortName;

    /**
     * @var string|null
     *
     * @Assert\Length(max=100)
     * @Assert\NotBlank(
     *     message="Необходимо указать полное название компании",
     *     groups={"Company"}
     * )
     *
     * @ORM\Column(name="large_name", type="string", length=100)
     */
    private $largeName;

    /**
     * @var string|null
     *
     * @Assert\Length(min=10, max=12)
     * @Assert\NotBlank(
     *     message="Необходимо указать ИНН",
     *     groups={"Company"}
     * )
     *
     * @ORM\Column(name="inn", type="string", length=12)
     */
    private $inn;

    /**
     * @var string|null
     *
     * @Assert\Length(min=13, max=15)
     * @Assert\NotBlank(
     *     message="Необходимо указать ОГРН",
     *     groups={"Company"}
     * )
     *
     * @ORM\Column(name="ogrn", type="string", length=15)
     */
    private $ogrn;

    /**
     * @var string|null
     *
     * @Assert\Length(min=9, max=9)
     *
     * @ORM\Column(name="kpp", type="string", length=9, nullable=true)
     */
    private $kpp;

    /**
     * @var string|null
     *
     * @Assert\Length(min=9, max=9)
     * @Assert\NotBlank(
     *     message="Необходимо указать БИК",
     *     groups={"Company"}
     * )
     *
     * @ORM\Column(name="bik", type="string", length=9)
     */
    private $bik;

    /**
     * @var string|null
     *
     * @Assert\Length(min=20, max=25)
     * @Assert\NotBlank(
     *     message="Необходимо указать расчётный счёт",
     *     groups={"Company"}
     * )
     *
     * @ORM\Column(name="account_number", type="string", length=25)
     */
    private $accountNumber;

    /**
     * @var string|null
     *
     * @Assert\Length(max=150)
     * @Assert\NotBlank(
     *     message="Необходимо указать адрес",
     *     groups={"Company"}
     * )
     *
     * @ORM\Column(name="address", type="string", length=150)
     */
    private $address;

    /**
     * @param User $user
     *
     * @return Company
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
     * @param string|null $shortName
     *
     * @return Company
     */
    public function setShortName(?string $shortName): self
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    /**
     * @param string|null $largeName
     *
     * @return Company
     */
    public function setLargeName(?string $largeName): self
    {
        $this->largeName = $largeName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLargeName(): ?string
    {
        return $this->largeName;
    }

    /**
     * @param string|null $inn
     *
     * @return Company
     */
    public function setInn(?string $inn): self
    {
        $this->inn = $inn;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getInn(): ?string
    {
        return $this->inn;
    }

    /**
     * @param string|null $ogrn
     *
     * @return Company
     */
    public function setOgrn(?string $ogrn): self
    {
        $this->ogrn = $ogrn;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOgrn(): ?string
    {
        return $this->ogrn;
    }

    /**
     * @param string|null $kpp
     *
     * @return Company
     */
    public function setKpp(?string $kpp): self
    {
        $this->kpp = $kpp;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getKpp(): ?string
    {
        return $this->kpp;
    }

    /**
     * @param string|null $bik
     *
     * @return Company
     */
    public function setBik(?string $bik): self
    {
        $this->bik = $bik;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBik(): ?string
    {
        return $this->bik;
    }

    /**
     * @param string|null $accountNumber
     *
     * @return Company
     */
    public function setAccountNumber(?string $accountNumber): self
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    /**
     * @param string|null $address
     *
     * @return Company
     */
    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }
}
