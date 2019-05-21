<?php

namespace AppBundle\Event;

use AppBundle\Entity\Lead;
use Symfony\Component\EventDispatcher\Event;

class LeadEvent extends Event
{
    const NEW_LEAD_PLACED = 'new_lead_placed';

    /**
     * @var Lead
     */
    private $lead;

    /**
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    /**
     * @return Lead
     */
    public function getLead(): Lead
    {
        return $this->lead;
    }
}