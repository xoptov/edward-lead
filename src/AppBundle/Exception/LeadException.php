<?php

namespace AppBundle\Exception;

use AppBundle\Entity\Lead;
use Throwable;

class LeadException extends \Exception
{
    /**
     * @var Lead
     */
    private $lead;

    /**
     * @param Lead           $lead
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(Lead $lead, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

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