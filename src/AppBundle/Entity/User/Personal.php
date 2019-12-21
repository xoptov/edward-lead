<?php

namespace AppBundle\Entity\User;

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
     * @var \DateTime|null
     *
     * @Assert\Date(message="Не верный формат даты рождения")
     *
     * @ORM\Column(name="birth_date", type="date", nullable=true)
     */
    private $birthDate;

    /**
     * @var Passport|null
     *
     * @ORM\Embedded(class="AppBundle\Entity\User\Personal\Passport")
     */
    private $passport;
}