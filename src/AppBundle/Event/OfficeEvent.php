<?php

namespace AppBundle\Event;

use AppBundle\Entity\Office;
use Symfony\Component\EventDispatcher\Event;

class OfficeEvent extends Event
{
    const NEW_CREATED = 'office.new_created';
    const UPDATED     = 'office.updated';

    /**
     * @var Office
     */
    private $office;

    /**
     * @param Office $office
     */
    public function __construct(Office $office)
    {
        $this->office = $office;
    }

    /**
     * @return Office
     */
    public function getOffice(): Office
    {
        return $this->office;
    }
}