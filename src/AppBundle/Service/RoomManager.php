<?php

namespace AppBundle\Service;

use AppBundle\Util\Math;
use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use AppBundle\Entity\Member;
use AppBundle\Exception\RoomException;
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
     * @param bool $flush
     *
     * @return Member
     *
     * @throws RoomException
     */
    public function joinInRoom(Room $room, User $user, bool $flush = true): Member
    {
        if ($this->isMember($room, $user)) {
            throw new RoomException($room, $user, 'Пользователь уже находится в группе');
        }

        // Запретить добавляться в комнату если включен таймер и уже есть пользователь такого-же типа.
        // @todo: Это необходимо вынести в другой сервис, и сделать интерфейс общий или абстрактный класс.
        if ($room->isTimer() && $room->getId()) {

            $members = $this->entityManager->getRepository(Member::class)
                ->getByRooms([$room]);

            $webmasters = 0;
            $advertisers = 0;

            /** @var Member $member */
            foreach ($members as $member) {
                if ($member->isWebmaster()) {
                    $webmasters++;
                } elseif ($member->isCompany()) {
                    $advertisers++;
                }
            }

            if (($user->isWebmaster() && $webmasters)
                || ($user->isAdvertiser() && $advertisers)) {
                throw new RoomException($room, $user, 'Комната с таймером и такой тип пользователя уже есть в комнате');
            }
        }

        $member = new Member();
        $member
            ->setUser($user)
            ->setRoom($room);
        $this->entityManager->persist($member);

        if ($flush) {
            $this->entityManager->flush();
        }

        return $member;
    }

    /**
     * @param Room  $room
     * @param float $buyerFee
     * @param int   $availableBalance
     *
     * @return int
     */
    public function countCanBuy(
        Room $room,
        float $buyerFee,
        int $availableBalance
    ): int {

        if ($availableBalance === 0) {
            return 0;
        }

        if ($room->hasHiddenMargin()) {
            $leadPrice = $room->getLeadPrice() + $room->getHiddenMargin();
        } else {
            $leadPrice = $room->getLeadPrice();
        }

        $leadPriceWithFee = $leadPrice + Math::calculateByInterest($leadPrice, $buyerFee);

        return floor($availableBalance / $leadPriceWithFee);
    }
}