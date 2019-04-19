<?php

namespace AppBundle\Exception;

use Throwable;
use AppBundle\Entity\Operation;

class OperationException extends \Exception
{
    /**
     * @var Operation
     */
    private $operation;

    /**
     * @param Operation      $operation
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(Operation $operation, string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->operation = $operation;
    }

    /**
     * @return Operation
     */
    public function getOperation(): Operation
    {
        return $this->operation;
    }
}