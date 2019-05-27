<?php

namespace AppBundle\Event;

use AppBundle\Entity\Lead;
use Symfony\Component\EventDispatcher\Event;

class LeadEvent extends Event
{
    const NEW_LEAD_PLACED = 'new_lead_placed';
    const LEAD_RESERVED = 'lead_reserved';
    const LEAD_SOLD = 'lead_sold';
    const LEAD_BLOCK_BY_REJECT = 'lead_block_by_reject';

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