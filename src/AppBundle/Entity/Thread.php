<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\MessageBundle\Entity\Thread as BaseThread;

/**
 * @ORM\Entity
 */
class Thread extends BaseThread
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @var ParticipantInterface
     */
    protected $createdBy;

    /**
     * @ORM\OneToMany(
     *   targetEntity="AppBundle\Entity\Message",
     *   mappedBy="thread"
     * )
     * @var Message[]|Collection
     */
    protected $messages;

    /**
     * @ORM\OneToMany(
     *   targetEntity="AppBundle\Entity\ThreadMetadata",
     *   mappedBy="thread",
     *   cascade={"all"}
     * )
     * @var ThreadMetadata[]|Collection
     */
    protected $metadata;

    /**
     * const of status
     */
    const STATUS_NEW          = 'new';
    const STATUS_WAIT_USER    = 'wait_user';
    const STATUS_WAIT_SUPPORT = 'wait_support';
    const STATUS_CLOSED       = 'closed';

    /**
     * @var string|null
     *
     * @ORM\Column(name="status", type="string", length=20)
     */
    protected $status;

    /**
     * const of type appeal
     */
    const TYPE_ARBITRATION = 'arbitration';
    const TYPE_SUPPORT     = 'support';

    /**
     * @var string|null
     *
     * @ORM\Column(name="type_appeal", type="string", length=20)
     */
    protected $typeAppeal;

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     * @return Thread
     */
    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTypeAppeal(): ?string
    {
        return $this->typeAppeal;
    }

    /**
     * @param string|null $typeAppeal
     * @return Thread
     */
    public function setTypeAppeal(?string $typeAppeal): self
    {
        $this->typeAppeal = $typeAppeal;

        return $this;
    }
}