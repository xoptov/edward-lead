<?php

namespace AppBundle\Exception;

use Throwable;
use AppBundle\Entity\Lead;
use AppBundle\Entity\User;

class PhoneCallException extends \Exception
{
    /**
     * @var User
     */
    private $caller;

    /**
     * @var Lead
     */
    private $lead;

    /**
     * @param User           $caller
     * @param Lead           $lead
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(User $caller, Lead $lead, string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->caller = $caller;
        $this->lead = $lead;
    }

    /**
     * @return User
     */
    public function getCaller(): User
    {
        return $this->caller;
    }

    /**
     * @return Lead
     */
    public function getLead(): Lead
    {
        return $this->lead;
    }
}