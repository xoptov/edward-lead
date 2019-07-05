<?php

namespace AppBundle\Entity\Part;

use Doctrine\ORM\Mapping as ORM;

trait CreatedAtTrait
{
    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @param int|null $multiple
     *
     * @return int|null
     */
    public function getCreatedAtTimestamp(?int $multiple = 1000): ?int
    {
        if ($this->createdAt) {
            return $this->createdAt->getTimestamp() * $multiple;
        }

        return null;
    }
}