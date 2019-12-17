<?php

namespace AppBundle\Entity\Room\Schedule;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
class WorkTime
{
    /**
     * @var \DateTime|null
     *
     * @Assert\NotBlank(
     *     message="Необходимо указать начало рабочего времени",
     *     groups={"timer"}
     * )
     *
     * @ORM\Column(name="start_at", type="time", nullable=true)
     */
    private $startAt;

    /**
     * @var \DateTime|null
     *
     * @Assert\NotBlank(
     *     message="Необходимо указать конец рабочего времени",
     *     groups={"timer"}
     * )
     *
     * @ORM\Column(name="end_at", type="time", nullable=true)
     */
    private $endAt;

    /**
     * @param \DateTime|null $startAt
     *
     * @return WorkTime
     */
    public function setStartAt(?\DateTime $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getStartAt(): ?\DateTime
    {
        return $this->startAt;
    }

    /**
     * @param \DateTime|null $endAt
     *
     * @return WorkTime
     */
    public function setEndAt(?\DateTime $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getEndAt(): ?\DateTime
    {
        return $this->endAt;
    }
}
