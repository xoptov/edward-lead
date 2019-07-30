<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Part\EnabledTrait;
use AppBundle\Entity\Part\IdentificatorTrait;
use AppBundle\Entity\Part\TimeTrackableTrait;

/**
 * @ORM\Table(name="country")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Country implements IdentifiableInterface
{
    use IdentificatorTrait;

    use TimeTrackableTrait;

    use EnabledTrait;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=30, unique=true)
     */
    private $name;

    /**
     * @param string $name
     *
     * @return Country
     */
    public function setName(string $name): self
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
}

