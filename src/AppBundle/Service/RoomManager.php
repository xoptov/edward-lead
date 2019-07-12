<?php

namespace AppBundle\Service;

use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use AppBundle\Entity\Member;
use Doctrine\ORM\EntityManagerInterface;

class RoomManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Room $room
     */
    public function updateInviteToken(Room $room)
    {
        $token = md5(time() + rand(1, 1000));
        $room->setInviteToken($token);
    }

    /**
     * @param Room $room
     * @param User $user
     *
     * @return bool
     */
    public function isMember(Room $room, User $user): bool
    {
        $member = $this->entityManager->getRepository(Member::class)
            ->findOneBy(['room' => $room, 'user' => $user]);

        return $member instanceof Member;
    }

    /**
     * @param Room $room
     * @param User $user
     *
     * @return Member
     *
     * @throws \Exception
     */
    public function joinInRoom(Room $room, User $user): Member
    {
        if (!$this->isMember($room, $user)) {
            throw new \Exception('Пользователь уже находится в группе');
        }

        $member = new Member();
        $member
            ->setUser($user)
            ->setRoom($room);
        $this->entityManager->persist($member);

        return $member;
    }
}