<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use AppBundle\Entity\Part\TimeTrackableTrait;
use AppBundle\Entity\Part\IdentificatorTrait;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @var Image|null
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Image")
     * @ORM\JoinColumn(name="logotype_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $logotype;

    /**
     * @var string
     */
    private $logotypePath;

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
     * @var string|null
     *
     * @Assert\Length(min=6, max=6)
     * @Assert\NotBlank(
     *     message="Необходимо указать почтовый индекс",
     *     groups={"Company"}
     * )
     *
     * @ORM\Column(name="zipcode", type="string", length=6)
     */
    private $zipcode;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\City")
     * @ORM\JoinTable(name="companies_cities")
     */
    private $cities;

    /**
     * @var string|null
     *
     * @Assert\Length(max=30)
     * @Assert\NotBlank(
     *     message="Необходимо указать название офиса",
     *     groups={"Office"}
     * )
     *
     * @ORM\Column(name="office_name", type="string", length=30, nullable=true)
     */
    private $officeName;

    /**
     * @var string|null
     *
     * @Assert\Regex(
     *     pattern="/^7\d{10}$/",
     *     message="Невалидный формат телефона"
     * )
     * @Assert\NotBlank(
     *     message="Необходимо указать контактный телефон офиса",
     *     groups={"Office"}
     * )
     *
     * @ORM\Column(name="office_phone", type="string", length=32, nullable=true)
     */
    private $officePhone;

    /**
     * @var string|null
     *
     * @Assert\Length(max=150)
     * @Assert\NotBlank(
     *     message="Необходимо указать адрес офиса",
     *     groups={"Office"}
     * )
     *
     * @ORM\Column(name="office_address", type="string", length=150, nullable=true)
     */
    private $officeAddress;

    public function __construct()
    {
        $this->cities = new ArrayCollection();
    }

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
     * @param Image $logotype
     *
     * @return Company
     */
    public function setLogotype(Image $logotype): self
    {
        $this->logotype = $logotype;

        return $this;
    }

    /**
     * @return Image|null
     */
    public function getLogotype(): ?Image
    {
        return $this->logotype;
    }

    /**
     * @param string|null $logotypePath
     *
     * @return Company
     */
    public function setLogotypePath(?string $logotypePath): self
    {
        $this->logotypePath = $logotypePath;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLogotypePath(): ?string
    {
        return $this->logotypePath;
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

    /**
     * @param string|null $zipcode
     *
     * @return Company
     */
    public function setZipcode(?string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    /**
     * @param Collection $cities
     *
     * @return Company
     */
    public function setCities(Collection $cities): self
    {
        $this->cities = $cities;

        return $this;
    }

    /**
     * @return Collection|array
     */
    public function getCities(): Collection
    {
        return $this->cities;
    }

    /**
     * @param string|null $officeName
     *
     * @return Company
     */
    public function setOfficeName(?string $officeName): self
    {
        $this->officeName = $officeName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOfficeName(): ?string
    {
        return $this->officeName;
    }

    /**
     * @param string|null $officePhone
     *
     * @return Company
     */
    public function setOfficePhone(?string $officePhone): self
    {
        $this->officePhone = $officePhone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOfficePhone(): ?string
    {
        return $this->officePhone;
    }

    /**
     * @param string|null $officeAddress
     *
     * @return Company
     */
    public function setOfficeAddress(?string $officeAddress): self
    {
        $this->officeAddress = $officeAddress;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOfficeAddress(): ?string
    {
        return $this->officeAddress;
    }
}