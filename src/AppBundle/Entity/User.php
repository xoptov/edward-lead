<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\MessageBundle\Model\ParticipantInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"email"}, message="Пользователь с таким email уже существует")
 */
class User implements AdvancedUserInterface, ParticipantInterface
{
    use TimeTrackableTrait;

    const ROLE_USER        = 'ROLE_USER';
    const ROLE_COMPANY     = 'ROLE_COMPANY';
    const ROLE_WEBMASTER   = 'ROLE_WEBMASTER';
    const ROLE_ADMIN       = 'ROLE_ADMIN';
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    const DEFAULT_ROLE     = self::ROLE_USER;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"="true"})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Company|null
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Company", mappedBy="user")
     */
    private $company;

    /**
     * @var ClientAccount|null
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\ClientAccount", mappedBy="user")
     */
    private $account;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(message="Имя должно быть указано.")
     * @ORM\Column(name="name", type="string", length=30)
     */
    private $name;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(message="Телефон должен быть указан.")
     * @ORM\Column(name="phone", type="string", length=32)
     */
    private $phone;

    /**
     * @var string
     *
     * @Assert\Email(message="Невалидное значение поля")
     * @Assert\NotBlank(message="Значение в поле должно быть указано.")
     * @ORM\Column(name="email", type="string", length=30, unique=true)
     */
    private $email;

    /**
     * @var string|null
     *
     * @Assert\Length(max=30)
     * @ORM\Column(name="skype", type="string", length=30, nullable=true, unique=true)
     */
    private $skype;

    /**
     * @var string|null
     *
     * @Assert\Length(max=50)
     * @ORM\Column(name="vkontakte", type="string", length=50, nullable=true, unique=true)
     */
    private $vkontakte;

    /**
     * @var string|null
     *
     * @Assert\Length(max=50)
     * @ORM\Column(name="facebook", type="string", length=50, nullable=true, unique=true)
     */
    private $facebook;

    /**
     * @var string|null
     *
     * @Assert\Length(min=2, max=30)
     * @ORM\Column(name="telegram", type="string", length=30, nullable=true, unique=true)
     */
    private $telegram;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=60)
     */
    private $password;

    /**
     * @var string|null
     *
     * @Assert\Length(min=6, max=16)
     * @Assert\NotBlank(groups={"Registration"}, message="Пароль должен быть указан")
     */
    private $plainPassword;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="array")
     */
    private $roles = [];

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled = false;

    /**
     * @var string|null
     *
     * @ORM\Column(name="confirm_token", type="string", length=40, nullable=true)
     */
    private $confirmToken;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reset_token", type="string", length=40, nullable=true)
     */
    private $resetToken;

    /**
     * @var bool
     *
     * @ORM\Column(name="type_selected", type="boolean")
     */
    private $typeSelected = false;

    /**
     * @var int|null
     *
     * @ORM\Column(name="purchase_fee_fixed", type="integer", nullable=true)
     */
    private $purchaseFeeFixed;

    /**
     * @var float|null
     *
     * @ORM\Column(name="purchase_fee_percent", type="float", nullable=true)
     */
    private $purchaseFeePercent;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sale_lead_limit", type="integer", nullable=true)
     */
    private $saleLeadLimit;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public static function getPossibleRoles(): array
    {
        return [
            self::ROLE_COMPANY,
            self::ROLE_ADMIN,
            self::ROLE_SUPER_ADMIN,
            self::ROLE_WEBMASTER,
            self::ROLE_COMPANY
        ];
    }

    /**
     * @param Company|null $company
     *
     * @return User
     */
    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Company|null
     */
    public function getCompany(): ?Company
    {
        return $this->company;
    }

    /**
     * @param ClientAccount $account
     *
     * @return User
     */
    public function setAccount(ClientAccount $account): self
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return ClientAccount|null
     */
    public function getAccount(): ?ClientAccount
    {
        return $this->account;
    }

    /**
     * @return bool
     */
    public function hasAccount(): bool
    {
        return !empty($this->account);
    }

    /**
     * @param string|null $name
     *
     * @return User
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $phone
     *
     * @return User
     */
    public function setPhone(?string $phone): self
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
     * @return User
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
     * @param string|null $skype
     *
     * @return User
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
     * @return User
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
     * @return User
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

    /**
     * @param null|string $telegram
     *
     * @return User
     */
    public function setTelegram(?string $telegram): self
    {
        $this->telegram = $telegram;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTelegram(): ?string
    {
        return $this->telegram;
    }

    /**
     * @param string $password
     *
     * @return User
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function getSalt()
    {
        return null;
    }

    /**
     * @param string|null $plainPassword
     *
     * @return User
     */
    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * @param array $roles
     *
     * @return User
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = static::DEFAULT_ROLE;

        return array_unique($roles);
    }

    /**
     * @param string $role
     *
     * @return User
     */
    public function addRole(string $role): self
    {
        $roles = $this->roles;
        $roles[] = $role;
        $this->roles = array_unique($roles);

        return $this;
    }

    /**
     * @param string $role
     *
     * @return User
     */
    public function removeRole(string $role): self
    {
        if (in_array($role, $this->roles)) {
            $key = array_search($role, $this->roles);
            unset($this->roles[$key]);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * @param boolean $enabled
     *
     * @return User
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param null|string $confirmToken
     *
     * @return User
     */
    public function setConfirmToken(?string $confirmToken): self
    {
        $this->confirmToken = $confirmToken;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getConfirmToken(): ?string
    {
        return $this->confirmToken;
    }

    /**
     * @param null|string $resetToken
     *
     * @return User
     */
    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    /**
     * @inheritdoc
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function hasCompany(): bool
    {
        return $this->company instanceof Company;
    }

    /**
     * @return bool
     */
    public function isTypeSelected(): bool
    {
        return $this->typeSelected;
    }

    /**
     * @return User
     */
    public function makeTypeSelected(): self
    {
        if (!$this->typeSelected) {
            $this->typeSelected = true;
        }

        return $this;
    }

    /**
     * @param int|null $purchaseFeeFixed
     *
     * @return User
     */
    public function setPurchaseFeeFixed(?int $purchaseFeeFixed): self
    {
        $this->purchaseFeeFixed = $purchaseFeeFixed;

        return $this;
    }

    /**
     * @return User
     */
    public function switchToWebmaster(): self
    {
        $this
            ->removeRole(self::ROLE_COMPANY)
            ->addRole(self::ROLE_WEBMASTER);

        return $this;
    }

    /**
     * @return User
     */
    public function switchToCompany(): self
    {
        $this
            ->removeRole(self::ROLE_WEBMASTER)
            ->addRole(self::ROLE_COMPANY);

        return $this;
    }

    /**
     * @return int
     */
    public function getBalance(): int
    {
        if (!$this->account) {
            return 0;
        }

        return $this->account->getBalance();
    }

    /**
     * @return float
     */
    public function getHumanBalance(): float
    {
        return $this->getBalance() / Account::DIVISOR;
    }

    /**
     * @return int|null
     */
    public function getSaleLeadLimit(): ?int
    {
        return $this->saleLeadLimit;
    }
}

