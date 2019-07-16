<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Part\IdentificatorTrait;

/**
 * @ORM\Table(name="image")
 * @ORM\Entity
 */
class Image implements IdentifiableInterface
{
    use IdentificatorTrait;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=150, unique=true)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=40, unique=true)
     */
    private $filename;

    /**
     * @param string $path
     *
     * @return Image
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $filename
     *
     * @return Image
     */
    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }
}