<?php

namespace Tests\unit\AppBundle\Notifications;

use AppBundle\Entity\ClientAccount;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\Lead;
use AppBundle\Entity\Member;
use AppBundle\Entity\Message;
use AppBundle\Entity\Room;
use AppBundle\Entity\Thread;
use AppBundle\Entity\Trade;
use AppBundle\Entity\User;
use AppBundle\Entity\Withdraw;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

abstract class BaseNotificationContainerTestCase extends TestCase
{
    const MEMBERS_COUNT = 10;

    public function getAccount()
    {
        $account = new ClientAccount();
        $account->setUser($this->getUser());

        return $account;
    }

    public function getLead()
    {
        $lead = new Lead();
        $lead
            ->setId(1)
            ->setPhone('79000000003')
            ->setStatus(Lead::STATUS_EXPECT)
            ->setPrice(10000)
            ->setUser($this->getUser())
            ->setTrade($this->getTrade($lead))
            ->setRoom($this->getRoom());

        return $lead;
    }

    public function getMessage()
    {
        $thread = new Thread();
        $thread->setCreatedBy($this->getUser());

        $object = new Message();
        $object->setThread($thread);
        $object->setSender($this->getUser());

        return $object;

    }

    public function getUser($id = 1)
    {
        $account = new ClientAccount();
        $account->setType('type');

        $user = new User();
        $user
            ->setId($id)
            ->setName('Company 1')
            ->setEmail('company1@xoptov.ru')
            ->setPhone('79000000001')
            ->setPlainPassword(123456)
            ->setAccount($account);

        return $user;
    }

    public function getWithdraw()
    {
        $object = new Withdraw();
        $object->setUser($this->getUser());

        return $object;
    }

    public function getMember($id = 1)
    {

        $object = new Member();
        $object
            ->setId($id)
            ->setUser($this->getUser())
            ->setRoom($this->getRoom());

        return $object;
    }

    public function getMembers()
    {

        $members = new ArrayCollection();

        for ($i = 0; $i < self::MEMBERS_COUNT; $i++) {

            $user = new User();
            $user
                ->setEmail("company{$i}@xoptov.ru")
                ->setRoles([User::ROLE_WEBMASTER, User::ROLE_COMPANY]);

            $member = new Member();
            $member->setUser($user);

            $members->add($member);
        }

        return $members;

    }

    public function getRoom()
    {
        $room = new Room();
        $room
            ->setId(1)
            ->setOwner($this->getUser())
            ->setName(' комната')
            ->setSphere(' сфера');
        return $room;
    }

    public function getTrade(Lead $lead = null)
    {
        $lead = $lead instanceof Lead ? $lead : $this->getLead();

        $room = new Trade();
        $room
            ->setSeller($this->getUser(1))
            ->setBuyer($this->getUser(2))
            ->setLead($lead);
        return $room;
    }

    public function getInvoice()
    {
        $invoice = new Invoice();
        $invoice->setUser($this->getUser());
        return $invoice;
    }
}