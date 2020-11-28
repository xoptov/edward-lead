<?php

namespace AppBundle\Entity\User;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\User\Personal\Passport;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
class Personal
{
    /**
     * @var string|null
     *
     * @Assert\Length(
     *     max=30,
     *     maxMessage="Максимальная длинна ФИО {{limit}} символов"
     * )
     *
     * @ORM\Column(name="full_name", type="string", length=30, nullable=true)
     */
    private $fullName;

    /**
     * @var DateTime|null
     *
     * @Assert\Date(message="Не верный формат даты рождения")
     *
     * @ORM\Column(name="birth_date", type="date", nullable=true)
     */
    private $birthDate;

    /**
     * @var Passport|null
     * 
     * @Assert\Valid(groups={"personal"})
     *
     * @ORM\Embedded(class="AppBundle\Entity\User\Personal\Passport")
     */
    private $passport;

    /**
     * @return string|null
     */
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * @param string|null $fullName
     * 
     * @return Personal
     */
    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getBirthDate(): ?DateTime
    {
        if ($this->birthDate) {
            return clone $this->birthDate;
        }

        return null;
    }

    /**
     * @param DateTime|null $birthDate
     * 
     * @return Personal
     */
    public function setBirthDate(?DateTime $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * @return Passport|null
     */
    public function getPassport(): ?Passport
    {
        if ($this->passport) {
            return clone $this->passport;
        }

        return null;
    }

    /**
     * @param Passport|null $passport
     *
     * @return Personal
     */
    public function setPassport(?Passport $passport): self
    {
        $this->passport = $passport;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasBirthDate(): bool
    {
        return !empty($this->birthDate);
    }
}
