<?php

namespace AppBundle\Event;

use AppBundle\Entity\Lead;
use Symfony\Component\EventDispatcher\Event;

class LeadEvent extends Event
{
    const NEW_PLACED      = 'new.lead.placed';
    const EDITED          = 'lead.edited';
    const RESERVED        = 'lead.reserved';
    const SOLD            = 'lead.sold';
    const BLOCK_BY_REJECT = 'lead.block_by_reject';

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