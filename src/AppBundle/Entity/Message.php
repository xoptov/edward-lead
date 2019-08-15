<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Doctrine\Common\Collections\Collection;
use FOS\MessageBundle\Model\ThreadInterface;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\MessageBundle\Entity\Message as BaseMessage;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Message extends BaseMessage implements IdentifiableInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var ThreadInterface
     *
     * @Assert\NotBlank(message="Необходимо указать нить сообщения")
     *
     * @ORM\ManyToOne(
     *   targetEntity="AppBundle\Entity\Thread",
     *   inversedBy="messages"
     * )
     */
    protected $thread;

    /**
     * @var ParticipantInterface
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     */
    protected $sender;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Необходимо указать содержание сообщения")
     */
    protected $body;

    /**
     * @var MessageMetadata[]|Collection
     *
     * @ORM\OneToMany(
     *   targetEntity="AppBundle\Entity\MessageMetadata",
     *   mappedBy="message",
     *   cascade={"all"}
     * )
     */
    protected $metadata;

    /**
     * @var ArrayCollection|PersistentCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Image")
     * @ORM\JoinTable(
     *     name="message_images",
     *     joinColumns={
     *          @ORM\JoinColumn(name="message_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *          @ORM\JoinColumn(name="image_id", referencedColumnName="id", unique=true)
     *     }
     * )
     */
    protected $images;

    public function __construct()
    {
        parent::__construct();

        $this->images = new ArrayCollection();
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param ArrayCollection|PersistentCollection $images
     *
     * @return Message
     */
    public function setImages($images): self
    {
        $this->images = $images;

        return $this;
    }
}