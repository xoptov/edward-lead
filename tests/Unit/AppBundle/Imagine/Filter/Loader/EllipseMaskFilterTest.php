<?php

namespace Unit\AppBundle\Imagine\Filter\Loader;

use Imagine\Imagick\Imagine;
use PHPUnit\Framework\TestCase;
use AppBundle\Imagine\Filter\Loader\EllipseMaskFilter;

class EllipseMaskFilterTest extends TestCase
{
    public function testLoad()
    {
        $imagine = new Imagine();

        $imagePath = __DIR__ . '/fixtures/logotype.jpg';
        $image = $imagine->open($imagePath);

        $ellipseMaskFilter = new EllipseMaskFilter();
        $filteredImage = $ellipseMaskFilter->load($image);

        return;
    }
}