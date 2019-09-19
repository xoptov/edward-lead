<?php

namespace Tests\unit\AppBundle\Form\Type;

use AppBundle\Entity\Lead;
use AppBundle\Entity\User;
use AppBundle\Entity\Trade;
use AppBundle\Entity\PhoneCall;
use AppBundle\Service\UserManager;
use AppBundle\Entity\PBX\Callback;
use AppBundle\Entity\PBX\Shoulder;
use AppBundle\Form\Type\PBXCallbackType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PBXCallbackTypeTest extends KernelTestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        static::bootKernel();

        $this->container = static::$kernel->getContainer();
        $this->entityManager = $this->container->get('doctrine.orm.default_entity_manager');
        $this->userManager = $this->container->get(UserManager::class);
        $this->formFactory = $this->container->get('form.factory');

        $this->entityManager->beginTransaction();
    }

    /**
     * @inheritdoc
     *
     * @todo: вообщем нужно разобраться почему не работаю транзакции, раньше то работали без выгрузки ядра!!! - Может кэш???
     */
    public function tearDown()
    {
        $connection = $this->entityManager->getConnection();
        $transactionLevel = $connection->getTransactionNestingLevel();

        for ($x = 0; $x < $transactionLevel; $x++) {
            $this->entityManager->rollback();
        }

        static::ensureKernelShutdown();
    }

    public function testHandleRequest_withCallbackForFirstCase()
    {
        $phoneCall = $this->prepareTestEnvironment('0000000000.0001');

        $fieldsMap = [
            '[call_id]'         => '[phoneCall]',
            '[event]'           => '[event]',
            '[recording]'       => '[audioRecord]',
            '[call1_phone]'     => '[firstShoulder][phone]',
            '[call1_billsec]'   => '[firstShoulder][billSec]',
            '[call1_tarif]'     => '[firstShoulder][tariff]',
            '[call1_start_at]'  => '[firstShoulder][startAt]',
            '[call1_answer_at]' => '[firstShoulder][answerAt]',
            '[call1_hangup_at]' => '[firstShoulder][hangupAt]',
            '[call1_status]'    => '[firstShoulder][status]',
            '[call2_phone]'     => '[secondShoulder][phone]',
            '[call2_billsec]'   => '[secondShoulder][billSec]',
            '[call2_tarif]'     => '[secondShoulder][tariff]',
            '[call2_start_at]'  => '[secondShoulder][startAt]',
            '[call2_answer_at]' => '[secondShoulder][answerAt]',
            '[call2_hangup_at]' => '[secondShoulder][hangupAt]',
            '[call2_status]'    => '[secondShoulder][status]'
        ];

        $form = $this->formFactory->create(PBXCallbackType::class, null, ['fields_map' => $fieldsMap]);
        
        $data = [
            'event' => 'hangup',
            'call1_phone' => '79883310019',
            'call1_billsec' => '0',
            'call1_tarif' => 'mobile',
            'call1_start_at' => '1567240573',
            'call1_answer_at' => '',
            'call1_hangup_at' => '1567240603',
            'call1_status' => 'cancel',
            'recording' => 'some_url',
            'call_id' => '0000000000.0001'
        ];

        $request = new Request([], $data);
        $request->setMethod(Request::METHOD_POST);

        $form->handleRequest($request);

        $result = $form->getData();

        $this->assertInstanceOf(Callback::class, $result);
        /** @var Callback $result */
        $this->assertEquals('hangup', $result->getEvent());
        $this->assertEquals($phoneCall, $result->getPhoneCall());
        $this->assertEquals('some_url', $result->getAudioRecord());

        $this->assertInstanceOf(Shoulder::class, $result->getFirstShoulder());
        $firstShoulder = $result->getFirstShoulder();

        $this->assertEquals('79883310019', $firstShoulder->getPhone());
        $this->assertEquals(0, $firstShoulder->getBillSec());
        $this->assertEquals('mobile', $firstShoulder->getTariff());
        $this->assertInstanceOf(\DateTime::class, $firstShoulder->getStartAt());
        $this->assertNull($firstShoulder->getAnswerAt());
        $this->assertInstanceOf(\DateTime::class, $firstShoulder->getHangupAt());
        $this->assertEquals('cancel', $firstShoulder->getStatus());

        $this->assertInstanceOf(Shoulder::class, $result->getSecondShoulder());
        $secondShoulder = $result->getSecondShoulder();

        $this->assertNull($secondShoulder->getPhone());
        $this->assertNull($secondShoulder->getBillSec());
        $this->assertNull($secondShoulder->getTariff());
        $this->assertNull($secondShoulder->getStartAt());
        $this->assertNull($secondShoulder->getAnswerAt());
        $this->assertNull($secondShoulder->getHangupAt());
        $this->assertNull($secondShoulder->getStatus());
    }

    public function testHandleRequest_withCallbackForThirdCase()
    {
        $phoneCall = $this->prepareTestEnvironment('0000000000.0002');

        $fieldsMap = [
            '[call_id]'         => '[phoneCall]',
            '[event]'           => '[event]',
            '[recording]'       => '[audioRecord]',
            '[call1_phone]'     => '[firstShoulder][phone]',
            '[call1_billsec]'   => '[firstShoulder][billSec]',
            '[call1_tarif]'     => '[firstShoulder][tariff]',
            '[call1_start_at]'  => '[firstShoulder][startAt]',
            '[call1_answer_at]' => '[firstShoulder][answerAt]',
            '[call1_hangup_at]' => '[firstShoulder][hangupAt]',
            '[call1_status]'    => '[firstShoulder][status]',
            '[call2_phone]'     => '[secondShoulder][phone]',
            '[call2_billsec]'   => '[secondShoulder][billSec]',
            '[call2_tarif]'     => '[secondShoulder][tariff]',
            '[call2_start_at]'  => '[secondShoulder][startAt]',
            '[call2_answer_at]' => '[secondShoulder][answerAt]',
            '[call2_hangup_at]' => '[secondShoulder][hangupAt]',
            '[call2_status]'    => '[secondShoulder][status]'
        ];

        $form = $this->formFactory->create(PBXCallbackType::class, null, ['fields_map' => $fieldsMap]);

        $data = [
            'event' => 'hangup',
            'call1_phone' => '79883310019',
            'call1_billsec' => '43',
            'call1_tarif' => 'mobile',
            'call1_start_at' => '1567241474',
            'call1_answer_at' => '1567241483',
            'call1_hangup_at' => '1567241526',
            'call1_status' => 'answer',
            'call2_phone' => '79892969151',
            'call2_billsec' => '0',
            'call2_tarif' => 'mobile',
            'call2_start_at' => '1567241926',
            'call2_answer_at' => '',
            'call2_hangup_at' => '1567241934',
            'call2_status' => 'cancel',
            'recording' => 'some_url',
            'call_id' => '0000000000.0002'
        ];

        $request = new Request([], $data);
        $request->setMethod(Request::METHOD_POST);

        $form->handleRequest($request);

        $result = $form->getData();

        $this->assertInstanceOf(Callback::class, $result);
        /** @var Callback $result */
        $this->assertEquals('hangup', $result->getEvent());
        $this->assertEquals($phoneCall, $result->getPhoneCall());
        $this->assertEquals('some_url', $result->getAudioRecord());

        $this->assertInstanceOf(Shoulder::class, $result->getFirstShoulder());
        $firstShoulder = $result->getFirstShoulder();

        $this->assertEquals('79883310019', $firstShoulder->getPhone());
        $this->assertEquals(43, $firstShoulder->getBillSec());
        $this->assertEquals('mobile', $firstShoulder->getTariff());
        $this->assertInstanceOf(\DateTime::class, $firstShoulder->getStartAt());
        $this->assertInstanceOf(\DateTime::class, $firstShoulder->getAnswerAt());
        $this->assertInstanceOf(\DateTime::class, $firstShoulder->getHangupAt());
        $this->assertEquals('answer', $firstShoulder->getStatus());

        $this->assertInstanceOf(Shoulder::class, $result->getSecondShoulder());
        $secondShoulder = $result->getSecondShoulder();

        $this->assertEquals('79892969151', $secondShoulder->getPhone());
        $this->assertEquals(0, $secondShoulder->getBillSec());
        $this->assertEquals('mobile', $secondShoulder->getTariff());
        $this->assertInstanceOf(\DateTime::class, $secondShoulder->getStartAt());
        $this->assertNull($secondShoulder->getAnswerAt());
        $this->assertInstanceOf(\DateTime::class, $secondShoulder->getHangupAt());
        $this->assertEquals('cancel', $secondShoulder->getStatus());
    }

    /**
     * @param string $callId
     *
     * @return PhoneCall
     */
    private function prepareTestEnvironment(string $callId): PhoneCall
    {
        $buyer = $this->createBuyer();
        $seller = $this->createSeller();
        $lead = $this->createLead($seller);
        $trade = $this->createTrade($buyer, $seller, $lead);
        $phoneCall = $this->createPhoneCall($callId, $buyer, $trade);

        $this->entityManager->flush();

        return $phoneCall;
    }

    /**
     * @return User
     */
    private function createBuyer(): User
    {
        $buyer = new User();
        $buyer
            ->setName('Buyer')
            ->setEmail('buyer@xoptov.ru')
            ->setPhone('79883310019')
            ->setPlainPassword(123456)
            ->setEnabled(true)
            ->switchToCompany();

        $this->entityManager->persist($buyer);
        $this->userManager->updateUser($buyer, false);

        return $buyer;
    }

    /**
     * @return User
     */
    private function createSeller(): User
    {
        $seller = new User();
        $seller
            ->setName('Seller')
            ->setEmail('seller@xoptov.ru')
            ->setPhone('79000000002')
            ->setPlainPassword(123456)
            ->setEnabled(true)
            ->switchToWebmaster();

        $this->entityManager->persist($seller);
        $this->userManager->updateUser($seller, false);

        return $seller;
    }

    /**
     * @param User $seller
     *
     * @return Lead
     */
    private function createLead(User $seller): Lead
    {
        $lead = new Lead();
        $lead
            ->setPhone('79000000003')
            ->setExpirationDate(new \DateTime('+2 days'))
            ->setPrice(10000)
            ->setUser($seller);

        $this->entityManager->persist($lead);

        return $lead;
    }

    /**
     * @param User $buyer
     * @param User $seller
     * @param Lead $lead
     *
     * @return Trade
     */
    private function createTrade(User $buyer, User $seller, Lead $lead): Trade
    {
        $trade = new Trade();
        $trade
            ->setBuyer($buyer)
            ->setSeller($seller)
            ->setLead($lead);

        $this->entityManager->persist($trade);

        return $trade;
    }

    /**
     * @param User $caller
     * @param Trade $trade
     *
     * @return PhoneCall
     */
    private function createPhoneCall(string $callId, User $caller, Trade $trade): PhoneCall
    {
        $phoneCall = new PhoneCall();
        $phoneCall
            ->setExternalId($callId)
            ->setCaller($caller)
            ->setTrade($trade)
            ->setStatus(PhoneCall::STATUS_REQUESTED);

        $this->entityManager->persist($phoneCall);

        return $phoneCall;
    }
}
