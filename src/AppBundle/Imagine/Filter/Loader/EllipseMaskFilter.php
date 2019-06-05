<?php

namespace AppBundle\Imagine\Filter\Loader;

use Imagine\Image\Point;
use Imagine\Imagick\Imagine;
use Imagine\Image\Palette\RGB;
use Imagine\Image\ImageInterface;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;

class EllipseMaskFilter implements LoaderInterface
{
    /**
     * @var Imagine
     */
    private $imagine;

    public function __construct()
    {
        $this->imagine = new Imagine();
    }

    /**
     * @inheritdoc
     */
    public function load(ImageInterface $image, array $options = []): ImageInterface
    {
        $palette = new RGB();
        $box = $image->getSize();

        $maskImage = $this->imagine->create($box, $palette->color('#fff'));
        $maskImage
            ->draw()
            ->ellipse(
                new Point($box->getWidth() / 2, $box->getHeight() / 2),
                $box,
                $palette->color('#000'),
                true
            );

        $image->applyMask($maskImage->mask());

        return $image;
    }
}