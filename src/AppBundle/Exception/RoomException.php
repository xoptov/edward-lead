<?php

namespace AppBundle\Exception;

use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use Throwable;

class RoomException extends \Exception
{
    /**
     * @var Room
     */
    private $room;

    /**
     * @var User
     */
    private $user;

    /**
     * @param Room           $room
     * @param User           $user
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(Room $room, User $user, string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->room = $room;
        $this->user = $user;
    }

    /**
     * @return Room
     */
    public function getRoom(): Room
    {
        return $this->room;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}