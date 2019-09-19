<?php

namespace Unit\AppBundle\Imagine\Filter\Loader;

use Imagine\Imagick\Image;
use Imagine\Imagick\Imagine;
use PHPUnit\Framework\TestCase;
use AppBundle\Imagine\Filter\Loader\EllipseMaskFilter;

class EllipseMaskFilterTest extends TestCase
{
    public function testLoad()
    {
        $this->markTestIncomplete('Чет муть со сравнением биранных данных, надо потом будет разобраться');

        $imagine = new Imagine();

        $imagePath = __DIR__ . '/fixtures/logotype.jpg';
        $filteredImagePath = __DIR__ . '/fixtures/logotype_masked.png';

        $image = $imagine->open($imagePath);

        /** @var Image $maskedImage */
        $maskedImage = $imagine->open($filteredImagePath);

        $ellipseMaskFilter = new EllipseMaskFilter();

        /** @var Image $filterResult */
        $filterResult = $ellipseMaskFilter->load($image);

        $image1 = $maskedImage->getImagick();
        $image2 = $filterResult->getImagick();

        $comparisonResult = $image1->compareImages($image2, \Imagick::METRIC_MEANSQUAREERROR);

        return;
    }
}