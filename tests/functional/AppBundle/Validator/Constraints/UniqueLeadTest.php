<?php

namespace Tests\functional\AppBundle\Validator\Constraints;

use AppBundle\Admin\AccountAdmin;
use AppBundle\Entity\Lead;
use AppBundle\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UniqueLeadTest extends KernelTestCase
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        static::bootKernel();
        $container = static::$kernel->getContainer();
        $this->entityManager = $container->get('doctrine.orm.default_entity_manager');
        $this->validator = $container->get('validator');
    }

    public function testValidator_withUniqueLead_inExchange()
    {
        $lead = new Lead();
        $lead->setPhone('79000000001');

        $violations = $this->validator->validate($lead);

        $this->assertEmpty($violations);
    }

    public function testValidator_withNoUniqueLead_inExchange_andStatusExpect()
    {
        $this->entityManager->beginTransaction();

        $lead1 = $this->createLead('79000000001');

        $this->entityManager->persist($lead1);
        $this->entityManager->flush();

        $lead2 = new Lead();
        $lead2->setPhone('79000000001');

        $violations = $this->validator->validate($lead2);
        $this->assertCount(1, $violations);

        $this->entityManager->rollback();
    }

    public function testValidator_withNoUniqueLead_inExchange_andStatusInWork()
    {
        $this->entityManager->beginTransaction();

        $lead1 = $this->createLead('79000000001', null, Lead::STATUS_IN_WORK);

        $this->entityManager->persist($lead1);
        $this->entityManager->flush();

        $lead2 = $this->createLead('79000000001');

        $violations = $this->validator->validate($lead2);
        $this->assertCount(1, $violations);

        $this->entityManager->rollback();
    }

    public function testValidator_withNoUniqueLead_inExchange_andStatusArbitration()
    {
        $this->entityManager->beginTransaction();

        $lead1 = $this->createLead('79000000001', null, Lead::STATUS_ARBITRATION);

        $this->entityManager->persist($lead1);
        $this->entityManager->flush();

        $lead2 = $this->createLead('79000000001');

        $violations = $this->validator->validate($lead2);
        $this->assertCount(1, $violations);

        $this->entityManager->rollback();
    }

    public function testValidator_withUniqueLead_inExchange_andStatusArchive()
    {
        $this->entityManager->beginTransaction();

        $lead1 = $this->createLead('79000000001', null, Lead::STATUS_ARCHIVE);

        $this->entityManager->persist($lead1);
        $this->entityManager->flush();

        $lead2 = $this->createLead('79000000001');

        $violations = $this->validator->validate($lead2);
        $this->assertEmpty($violations);

        $this->entityManager->rollback();
    }

    public function testValidator_withUniqueLead_inExchange_andStatusTraget()
    {
        $this->entityManager->beginTransaction();

        $lead1 = $this->createLead('79000000001', null, Lead::STATUS_TARGET);

        $this->entityManager->persist($lead1);
        $this->entityManager->flush();

        $lead2 = $this->createLead('79000000001');

        $violations = $this->validator->validate($lead2);
        $this->assertEmpty($violations);

        $this->entityManager->rollback();
    }

    public function testValidator_withUniqueLead_inExchange_andStatusNotTarget()
    {
        $this->entityManager->beginTransaction();

        $lead1 = $this->createLead('79000000001', null, Lead::STATUS_NOT_TARGET);

        $this->entityManager->persist($lead1);
        $this->entityManager->flush();

        $lead2 = $this->createLead('79000000001');

        $violations = $this->validator->validate($lead2);
        $this->assertEmpty($violations);

        $this->entityManager->rollback();
    }

    public function testValidator_withUniqueLead_inRoom()
    {
        $this->entityManager->beginTransaction();

        $room = $this->createRoom('тестовая');

        $this->entityManager->persist($room);
        $this->entityManager->flush();

        $lead = $this->createLead('79000000001', $room, Lead::STATUS_EXPECT);

        $violations = $this->validator->validate($lead);
        $this->assertEmpty($violations);

        $this->entityManager->rollback();
    }

    public function testValidator_withNoUniqueLead_inRoom_andStatusExpect()
    {
        $this->entityManager->beginTransaction();

        $room = $this->createRoom('тестовая');

        $this->entityManager->persist($room);

        $lead1 = $this->createLead('79000000001', $room, Lead::STATUS_EXPECT);

        $this->entityManager->persist($lead1);
        $this->entityManager->flush();

        $lead2 = $this->createLead('79000000001', $room, Lead::STATUS_EXPECT);

        $violations = $this->validator->validate($lead2);
        $this->assertCount(1, $violations);

        $this->entityManager->rollback();
    }

    public function testValidator_withNoUniqueLead_inRoom_andStatusInWork()
    {
        $this->entityManager->beginTransaction();

        $room = $this->createRoom('тестовая');

        $this->entityManager->persist($room);

        $lead1 = $this->createLead('79000000001', $room, Lead::STATUS_IN_WORK);

        $this->entityManager->persist($lead1);
        $this->entityManager->flush();

        $lead2 = $this->createLead('79000000001', $room, Lead::STATUS_EXPECT);

        $violations = $this->validator->validate($lead2);
        $this->assertCount(1, $violations);

        $this->entityManager->rollback();
    }

    public function testValidator_withNoUniqueLead_inRoom_andStatusArbitration()
    {
        $this->entityManager->beginTransaction();

        $room = $this->createRoom('тестовая');

        $this->entityManager->persist($room);

        $lead1 = $this->createLead('79000000001', $room, Lead::STATUS_ARBITRATION);

        $this->entityManager->persist($lead1);
        $this->entityManager->flush();

        $lead2 = $this->createLead('79000000001', $room, Lead::STATUS_EXPECT);

        $violations = $this->validator->validate($lead2);
        $this->assertCount(1, $violations);

        $this->entityManager->rollback();
    }

    public function testValidator_withUniqueLead_inRoom_andStatusArchive()
    {
        $this->entityManager->beginTransaction();

        $room = $this->createRoom('тестовая');

        $this->entityManager->persist($room);

        $lead1 = $this->createLead('79000000001', $room, Lead::STATUS_ARCHIVE);

        $this->entityManager->persist($lead1);
        $this->entityManager->flush();

        $lead2 = $this->createLead('79000000001', $room, Lead::STATUS_EXPECT);

        $violations = $this->validator->validate($lead2);
        $this->assertEmpty($violations);

        $this->entityManager->rollback();
    }

    public function testValidator_withUniqueLead_inRoom_andStatusTarget()
    {
        $this->entityManager->beginTransaction();

        $room = $this->createRoom('тестовая');

        $this->entityManager->persist($room);

        $lead1 = $this->createLead('79000000001', $room, Lead::STATUS_TARGET);

        $this->entityManager->persist($lead1);
        $this->entityManager->flush();

        $lead2 = $this->createLead('79000000001', $room, Lead::STATUS_EXPECT);

        $violations = $this->validator->validate($lead2);
        $this->assertEmpty($violations);

        $this->entityManager->rollback();
    }

    public function testValidator_withUniqueLead_inRoom_andStatusNotTarget()
    {
        $this->entityManager->beginTransaction();

        $room = $this->createRoom('тестовая');

        $this->entityManager->persist($room);

        $lead1 = $this->createLead('79000000001', $room, Lead::STATUS_NOT_TARGET);

        $this->entityManager->persist($lead1);
        $this->entityManager->flush();

        $lead2 = $this->createLead('79000000001', $room, Lead::STATUS_EXPECT);

        $violations = $this->validator->validate($lead2);
        $this->assertEmpty($violations);

        $this->entityManager->rollback();
    }

    /**
     * @param string    $phone
     * @param string    $status
     * @param Room|null $room
     *
     * @return Lead
     */
    private function createLead(string $phone, ?Room $room = null, string $status = Lead::STATUS_EXPECT): Lead
    {
        $lead = new Lead();
        $lead
            ->setRoom($room)
            ->setPhone($phone)
            ->setExpirationDate(new \DateTime('+48 hours'))
            ->setPrice(100000)
            ->setStatus($status);

        return $lead;
    }

    /**
     * @param string $name
     *
     * @return Room
     */
    private function createRoom(string $name): Room
    {
        $room = new Room();
        $room->setName($name . ' комната')
            ->setSphere($name. ' сфера')
            ->setPlatformWarranty(false)
            ->setInviteToken(md5($name))
            ->setEnabled(true);

        return $room;
    }
}