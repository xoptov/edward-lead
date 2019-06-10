<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="company")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Company
{
    use TimeTrackableTrait;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"="true"})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

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
     * @Assert\Length(max=30)
     * @Assert\NotBlank(
     *     message="Необходимо указать короткое название компании",
     *     groups={"Company"}
     * )
     * @ORM\Column(name="short_name", type="string", length=30)
     */
    private $shortName;

    /**
     * @var string|null
     *
     * @Assert\Length(max=60)
     * @Assert\NotBlank(
     *     message="Необходимо указать полное название компании",
     *     groups={"Company"}
     * )
     * @ORM\Column(name="large_name", type="string", length=60)
     */
    private $largeName;

    /**
     * @var string|null
     *
     * @Assert\Length(min=11, max=18)
     * @Assert\NotBlank(
     *     message="Необходимо указать номер телефона",
     *     groups={"Company"}
     * )
     * @ORM\Column(name="phone", type="string", length=12)
     */
    private $phone;

    /**
     * @var string|null
     *
     * @Assert\Length(max=30)
     * @Assert\Email(message="Невалидное значение поля")
     * @Assert\NotBlank(
     *     message="Необходимо указать email",
     *     groups={"Company"}
     * )
     * @ORM\Column(name="email", type="string", length=30)
     */
    private $email;

    /**
     * @var string|null
     *
     * @Assert\Length(min=10, max=12)
     * @Assert\NotBlank(
     *     message="Необходимо указать ИНН",
     *     groups={"Company"}
     * )
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
     * @ORM\Column(name="ogrn", type="string", length=15)
     */
    private $ogrn;

    /**
     * @var string|null
     *
     * @Assert\Length(min=9, max=9)
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
     * @ORM\Column(name="office_name", type="string", length=30, nullable=true)
     */
    private $officeName;

    /**
     * @var string|null
     *
     * @Assert\Length(min=11, max=18)
     * @Assert\NotBlank(
     *     message="Необходимо указать контактный телефон офиса",
     *     groups={"Office"}
     * )
     * @ORM\Column(name="office_phone", type="string", length=12, nullable=true)
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
     * @ORM\Column(name="office_address", type="string", length=150, nullable=true)
     */
    private $officeAddress;

    public function __construct()
    {
        $this->cities = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
     * @param string $shortName
     *
     * @return Company
     */
    public function setShortName(string $shortName): self
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
     * @param string $largeName
     *
     * @return Company
     */
    public function setLargeName(string $largeName): self
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
     * @param string $phone
     *
     * @return Company
     */
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string $email
     *
     * @return Company
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $inn
     *
     * @return Company
     */
    public function setInn(string $inn): self
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
     * @param string $ogrn
     *
     * @return Company
     */
    public function setOgrn(string $ogrn): self
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
     * @param string $kpp
     *
     * @return Company
     */
    public function setKpp(string $kpp): self
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
     * @param string $bik
     *
     * @return Company
     */
    public function setBik(string $bik): self
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
     * @param string $accountNumber
     *
     * @return Company
     */
    public function setAccountNumber(string $accountNumber): self
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
     * @param string $address
     *
     * @return Company
     */
    public function setAddress(string $address): self
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
     * @param string $zipcode
     *
     * @return Company
     */
    public function setZipcode(string $zipcode): self
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
     * @return Collection
     */
    public function getCities(): Collection
    {
        return $this->cities;
    }

    /**
     * @param string $officeName
     *
     * @return Company
     */
    public function setOfficeName(string $officeName): self
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
     * @param string $officePhone
     *
     * @return Company
     */
    public function setOfficePhone(string $officePhone): self
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
     * @param string $officeAddress
     *
     * @return Company
     */
    public function setOfficeAddress(string $officeAddress): self
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