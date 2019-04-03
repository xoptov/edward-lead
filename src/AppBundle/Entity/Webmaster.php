<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="webmaster")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Webmaster
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
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User", inversedBy="webmaster")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=30)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=12)
     */
    private $phone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="skype", type="string", length=30, nullable=true, unique=true)
     */
    private $skype;

    /**
     * @var string|null
     *
     * @ORM\Column(name="vkontakte", type="string", length=50, nullable=true, unique=true)
     */
    private $vkontakte;

    /**
     * @var string|null
     *
     * @ORM\Column(name="facebook", type="string", length=50, nullable=true, unique=true)
     */
    private $facebook;

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
     * @return Webmaster
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
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $phone
     *
     * @return Webmaster
     */
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $skype
     *
     * @return Webmaster
     */
    public function setSkype(?string $skype): self
    {
        $this->skype = $skype;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSkype(): ?string
    {
        return $this->skype;
    }

    /**
     * @param string|null $vkontakte
     *
     * @return Webmaster
     */
    public function setVkontakte(?string $vkontakte): self
    {
        $this->vkontakte = $vkontakte;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getVkontakte(): ?string
    {
        return $this->vkontakte;
    }

    /**
     * @param string|null $facebook
     *
     * @return Webmaster
     */
    public function setFacebook(?string $facebook)
    {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFacebook(): ?string
    {
        return $this->facebook;
    }
}