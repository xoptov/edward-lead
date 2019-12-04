<?php

namespace AppBundle\Event;

use AppBundle\Entity\Lead;
use Symfony\Component\EventDispatcher\Event;

class LeadEvent extends Event
{
    const NEW_PLACED       = 'lead.new_placed';
    const EDITED           = 'lead.edited';
    const IN_WORK          = 'lead.in_work';
    const TARGET           = 'lead.target';
    const NOT_TARGET       = 'lead.not_target';
    const ARCHIVED         = 'lead.archived';
    const EXPECT_TOO_LONG  = 'lead.expect_too_long';
    const IN_WORK_TOO_LONG = 'lead.in_work_too_long';
    const NO_VISIT_TOO_LONG = 'lead.no_visit_too_long';

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