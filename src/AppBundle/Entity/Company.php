<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

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
     * @var Image
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Image")
     * @ORM\JoinColumn(name="logotype_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $logotype;

    /**
     * @var string
     *
     * @ORM\Column(name="short_name", type="string", length=30)
     */
    private $shortName;

    /**
     * @var string
     *
     * @ORM\Column(name="large_name", type="string", length=60)
     */
    private $largeName;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=12)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="inn", type="string", length=12, unique=true)
     */
    private $inn;

    /**
     * @var string
     *
     * @ORM\Column(name="ogrn", type="string", length=13, unique=true)
     */
    private $ogrn;

    /**
     * @var string
     *
     * @ORM\Column(name="kpp", type="string", length=9)
     */
    private $kpp;

    /**
     * @var string
     *
     * @ORM\Column(name="bik", type="string", length=9)
     */
    private $bik;

    /**
     * @var string
     *
     * @ORM\Column(name="account_number", type="string", length=25, unique=true)
     */
    private $accountNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=150)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="zipcode", type="string", length=6)
     */
    private $zipcode;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Region", inversedBy="companies")
     * @ORM\JoinTable(name="companies_regions")
     */
    private $regions;

    public function __construct()
    {
        $this->regions = new ArrayCollection();
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
     * @return Image
     */
    public function getLogotype(): Image
    {
        return $this->logotype;
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
     * @return string
     */
    public function getShortName(): string
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
     * @return string
     */
    public function getLargeName(): string
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
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
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
     * @return string
     */
    public function getInn(): string
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
     * @return string
     */
    public function getOgrn(): string
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
     * @return string
     */
    public function getKpp(): string
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
     * @return string
     */
    public function getBik(): string
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
     * @return string
     */
    public function getAccountNumber(): string
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
     * @return string
     */
    public function getAddress(): string
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
     * @return string
     */
    public function getZipcode(): string
    {
        return $this->zipcode;
    }
}