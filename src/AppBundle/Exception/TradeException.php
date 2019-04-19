<?php

namespace AppBundle\Exception;

use Throwable;
use AppBundle\Entity\Lead;
use AppBundle\Entity\User;

class TradeException extends \Exception
{
    /**
     * @var Lead
     */
    private $lead;

    /**
     * @var User
     */
    private $buyer;

    /**
     * @var User
     */
    private $seller;

    /**
     * @param Lead           $lead
     * @param User           $buyer
     * @param User           $seller
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(Lead $lead, User $buyer, User $seller, string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->lead = $lead;
        $this->buyer = $buyer;
        $this->seller = $seller;
    }

    /**
     * @return Lead
     */
    public function getLead(): Lead
    {
        return $this->lead;
    }

    /**
     * @return User
     */
    public function getBuyer(): User
    {
        return $this->buyer;
    }

    /**
     * @return User
     */
    public function getSeller(): User
    {
        return $this->seller;
    }
}