<?php

namespace AppBundle\Entity\PBX;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Shoulder
{
    const STATUS_NO_ANSWER = 'noanswer';
    const STATUS_ANSWER    = 'answer';
    const STATUS_BUSY      = 'busy';
    const STATUS_CANCEL    = 'cancel';

    const TARIFF_MOBILE = 'mobile';
    const TARIFF_CITY   = 'city';
    const TARIFF_LOCAL  = 'local';

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $phone;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $tariff;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $answerAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $hangupAt;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $billSec;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $status;

    /**
     * @param string|null $phone
     *
     * @return Shoulder
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
     * @param string|null $tariff
     *
     * @return Shoulder
     */
    public function setTariff(?string $tariff): self
    {
        $this->tariff = $tariff;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTariff(): ?string
    {
        return $this->tariff;
    }

    /**
     * @param \DateTime|null $startAt
     *
     * @return Shoulder
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
     * @param \DateTime|null $answerAt
     *
     * @return Shoulder
     */
    public function setAnswerAt(?\DateTime $answerAt): self
    {
        $this->answerAt = $answerAt;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getAnswerAt(): ?\DateTime
    {
        return $this->answerAt;
    }

    /**
     * @param \DateTime|null $hangupAt
     *
     * @return Shoulder
     */
    public function setHangupAt(?\DateTime $hangupAt): self
    {
        $this->hangupAt = $hangupAt;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getHangupAt(): ?\DateTime
    {
        return $this->hangupAt;
    }

    /**
     * @param int|null $billSec
     *
     * @return Shoulder
     */
    public function setBillSec(?int $billSec): self
    {
        $this->billSec = $billSec;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getBillSec(): ?int
    {
        return $this->billSec;
    }

    /**
     * @param string|null $status
     *
     * @return Shoulder
     */
    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getDurationInSecond(): int
    {
        $total = 0;

        if ($this->answerAt && $this->hangupAt) {

            $diff = $this->answerAt->diff($this->hangupAt);
            $total += $diff->s;

            if ($diff->i) {
                $total += $diff->i * 60;
            }

            if ($diff->h) {
                $total += $diff->h * 3600;
            }
        }

        return $total;
    }
}