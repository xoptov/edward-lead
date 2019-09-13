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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PBXCallbackTypeTest extends KernelTestCase
{
    /**
     * @var EntityManagerInterface
     */
    private static $entityManager;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        static::bootKernel();
        static::$entityManager = static::$kernel->getContainer()->get('doctrine.orm.default_entity_manager');
    }

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        static::$entityManager->beginTransaction();
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        static::$entityManager->rollback();
        static::$entityManager->clear();
    }

    /**
     * @inheritdoc
     */
    public static function tearDownAfterClass()
    {
        static::ensureKernelShutdown();
    }

    public function testHandleRequest_withCallbackForFirstCase()
    {
        $userManager = static::$kernel->getContainer()->get(UserManager::class);

        $buyer = new User();
        $buyer
            ->setName('Buyer')
            ->setEmail('buyer@xoptov.ru')
            ->setPhone('79883310019')
            ->setPlainPassword(123456)
            ->setEnabled(true)
            ->switchToCompany();

        static::$entityManager->persist($buyer);

        $userManager->updateUser($buyer, false);

        $seller = new User();
        $seller
            ->setName('Seller')
            ->setEmail('seller@xoptov.ru')
            ->setPhone('79000000002')
            ->setPlainPassword(123456)
            ->setEnabled(true)
            ->switchToWebmaster();

        static::$entityManager->persist($seller);

        $userManager->updateUser($seller, false);

        $lead = new Lead();
        $lead
            ->setPhone('79000000003')
            ->setExpirationDate(new \DateTime('+2 days'))
            ->setPrice(10000);

        static::$entityManager->persist($lead);

        $trade = new Trade();
        $trade
            ->setBuyer($buyer)
            ->setSeller($seller)
            ->setLead($lead);

        static::$entityManager->persist($trade);

        $phoneCall = new PhoneCall();
        $phoneCall
            ->setExternalId('1567240573.2939')
            ->setCaller($buyer)
            ->setTrade($trade)
            ->setStatus(PhoneCall::STATUS_REQUESTED);

        static::$entityManager->persist($phoneCall);
        static::$entityManager->flush();

        $formFactory = static::$kernel->getContainer()->get('form.factory');

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

        $form = $formFactory->create(PBXCallbackType::class, null, ['fields_map' => $fieldsMap]);
        
        $data = [
            'event' => 'hangup',
            'call1_phone' => '79883310019',
            'call1_billsec' => '0',
            'call1_tarif' => 'mobile',
            'call1_start_at' => '1567240573',
            'call1_answer_at' => '',
            'call1_hangup_at' => '1567240603',
            'call1_status' => 'cancel',
            'recording' => 'http://159.253.123.139:82/2019/08/31/force-79883310019-unknown-20190831-123613-1567240573.2940.wav',
            'call_id' => '1567240573.2939'
        ];

        $request = new Request([], $data);
        $request->setMethod(Request::METHOD_POST);

        $form->handleRequest($request);

        $result = $form->getData();

        $this->assertInstanceOf(Callback::class, $result);
        /** @var Callback $result */
        $this->assertEquals('hangup', $result->getEvent());
        $this->assertEquals($phoneCall, $result->getPhoneCall());
        $this->assertEquals('http://159.253.123.139:82/2019/08/31/force-79883310019-unknown-20190831-123613-1567240573.2940.wav', $result->getAudioRecord());

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
        $userManager = static::$kernel->getContainer()->get(UserManager::class);

        $buyer = new User();
        $buyer
            ->setName('Buyer')
            ->setEmail('buyer@xoptov.ru')
            ->setPhone('79883310019')
            ->setPlainPassword(123456)
            ->setEnabled(true)
            ->switchToCompany();

        static::$entityManager->persist($buyer);

        $userManager->updateUser($buyer, false);

        $seller = new User();
        $seller
            ->setName('Seller')
            ->setEmail('seller@xoptov.ru')
            ->setPhone('79000000002')
            ->setPlainPassword(123456)
            ->setEnabled(true)
            ->switchToWebmaster();

        static::$entityManager->persist($seller);

        $userManager->updateUser($seller, false);

        $lead = new Lead();
        $lead
            ->setPhone('79000000003')
            ->setExpirationDate(new \DateTime('+2 days'))
            ->setPrice(10000);

        static::$entityManager->persist($lead);

        $trade = new Trade();
        $trade
            ->setBuyer($buyer)
            ->setSeller($seller)
            ->setLead($lead);

        static::$entityManager->persist($trade);

        $phoneCall = new PhoneCall();
        $phoneCall
            ->setExternalId('1567241474.2951')
            ->setCaller($buyer)
            ->setTrade($trade)
            ->setStatus(PhoneCall::STATUS_REQUESTED);

        static::$entityManager->persist($phoneCall);
        static::$entityManager->flush();

        $formFactory = static::$kernel->getContainer()->get('form.factory');

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

        $form = $formFactory->create(PBXCallbackType::class, null, ['fields_map' => $fieldsMap]);

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
            'recording' => 'http://159.253.123.139:82/2019/08/31/force-79883310019-unknown-20190831-125114-1567241474.2952',
            'call_id' => '1567241474.2951'
        ];

        $request = new Request([], $data);
        $request->setMethod(Request::METHOD_POST);

        $form->handleRequest($request);

        $result = $form->getData();

        $this->assertInstanceOf(Callback::class, $result);
        /** @var Callback $result */
        $this->assertEquals('hangup', $result->getEvent());
        $this->assertEquals($phoneCall, $result->getPhoneCall());
        $this->assertEquals('http://159.253.123.139:82/2019/08/31/force-79883310019-unknown-20190831-125114-1567241474.2952', $result->getAudioRecord());

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
}
