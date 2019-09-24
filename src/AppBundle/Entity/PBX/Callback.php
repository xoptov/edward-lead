<?php

namespace AppBundle\Entity\PBX;

use AppBundle\Entity\PhoneCall;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Part\CreatedAtTrait;
use AppBundle\Entity\IdentifiableInterface;
use AppBundle\Entity\Part\IdentificatorTrait;

/**
 * @ORM\Table(name="pbx_callback")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Callback implements IdentifiableInterface
{
    use IdentificatorTrait;

    use CreatedAtTrait;

    const STATUS_NEW     = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_FAIL    = 2;

    /**
     * @var PhoneCall|null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PhoneCall", inversedBy="callbacks")
     * @ORM\JoinColumn(name="phone_call_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $phoneCall;

    /**
     * @var string|null
     *
     * @ORM\Column(name="event", type="string", length=6)
     */
    private $event;

    /**
     * @var Shoulder|null
     *
     * @ORM\Embedded(class="AppBundle\Entity\PBX\Shoulder")
     */
    private $firstShoulder;

    /**
     * @var Shoulder|null
     *
     * @ORM\Embedded(class="AppBundle\Entity\PBX\Shoulder")
     */
    private $secondShoulder;

    /**
     * @var string|null
     *
     * @ORM\Column(name="audio_record", type="string", length=255, nullable=true)
     */
    private $audioRecord;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint", options={"unsigned":true})
     */
    private $status = self::STATUS_NEW;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param PhoneCall $phoneCall
     *
     * @return Callback
     */
    public function setPhoneCall(PhoneCall $phoneCall): self
    {
        $this->phoneCall = $phoneCall;

        return $this;
    }

    /**
     * @return PhoneCall|null
     */
    public function getPhoneCall(): ?PhoneCall
    {
        return $this->phoneCall;
    }

    /**
     * @param string $event
     *
     * @return Callback
     */
    public function setEvent(string $event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEvent(): ?string
    {
        return $this->event;
    }

    /**
     * @param Shoulder $firstShoulder
     *
     * @return Callback
     */
    public function setFirstShoulder(Shoulder $firstShoulder): self
    {
        $this->firstShoulder = $firstShoulder;

        return $this;
    }

    /**
     * @return Shoulder|null
     */
    public function getFirstShoulder(): ?Shoulder
    {
        return $this->firstShoulder;
    }

    /**
     * @param Shoulder $secondShoulder
     *
     * @return Callback
     */
    public function setSecondShoulder(Shoulder $secondShoulder): self
    {
        $this->secondShoulder = $secondShoulder;

        return $this;
    }

    /**
     * @return Shoulder|null
     */
    public function getSecondShoulder(): ?Shoulder
    {
        return $this->secondShoulder;
    }

    /**
     * @param string $audioRecord
     *
     * @return Callback
     */
    public function setAudioRecord(string $audioRecord): self
    {
        $this->audioRecord = $audioRecord;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAudioRecord(): ?string
    {
        return $this->audioRecord;
    }

    /**
     * @param int $status
     *
     * @return Callback
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return self::STATUS_SUCCESS === $this->status;
    }

    /**
     * @return bool
     */
    public function isFail(): bool
    {
        return self::STATUS_FAIL === $this->status;
    }

    /**
     * @return int
     */
    public function getTotalBillSec(): int
    {
        $total = 0;

        $total += $this->firstShoulder->getBillSec();
        $total += $this->secondShoulder->getBillSec();

        return $total;
    }
}