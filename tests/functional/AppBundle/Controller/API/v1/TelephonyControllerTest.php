<?php

namespace Tests\functional\AppBundle\Controller\API\v1;

use AppBundle\Entity\Lead;
use AppBundle\Entity\User;
use AppBundle\Entity\Account;
use AppBundle\Entity\Company;
use AppBundle\Entity\PhoneCall;
use AppBundle\Entity\PBX\Callback;
use AppBundle\Entity\PBX\Shoulder;
use AppBundle\Service\UserManager;
use AppBundle\Service\TradeManager;
use AppBundle\Entity\ClientAccount;
use Symfony\Component\Routing\Router;
use AppBundle\Service\PhoneCallManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TelephonyControllerTest extends WebTestCase
{
    /**
     * @var Client
     */
    private $client;

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
     * @var TradeManager
     */
    private $tradeManager;

    /**
     * @var PhoneCallManager
     */
    private $phoneCallManager;

    /**
     * @var Router
     */
    private $router;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->client = static::createClient();
        $this->container = static::$kernel->getContainer();
        $this->entityManager = $this->container->get('doctrine.orm.default_entity_manager');
        $this->userManager = $this->container->get(UserManager::class);
        $this->tradeManager = $this->container->get(TradeManager::class);
        $this->phoneCallManager = $this->container->get(PhoneCallManager::class);
        $this->router = $this->container->get('router');
        $this->entityManager->beginTransaction();
    }

    /**
     * @inheritdoc
     *
     * @todo: вообщем нужно разобраться почему не работаю транзакции, раньше то работали!!!
     */
    public function tearDown()
    {
        $connection = $this->entityManager->getConnection();
        $transactionLevel = $connection->getTransactionNestingLevel();

        for ($x = 0; $x < $transactionLevel; $x++) {
            $this->entityManager->rollback();
        }
    }

    public function testPostCallbackAction_withCase_1()
    {
        $this->prepareTestEnvironment('0000000000.0001');

        $requestData = [
            'event' => 'hangup',
            'call1_phone' => '79000000004',
            'call1_billsec' => '0',
            'call1_tarif' => 'mobile',
            'call1_start_at' => '1568714580',
            'call1_answer_at' => '',
            'call1_hangup_at' => '1568714610',
            'call1_status' => 'cancel',
            'recording' => 'some_url',
            'call_id' => '0000000000.0001'
        ];

        $response = $this->makeRequestToAction($requestData);

        $this->assertTrue($response->isSuccessful());

        /** @var PhoneCall $phoneCall */
        $phoneCall = $this->entityManager->getRepository(PhoneCall::class)
            ->findOneBy([
                'externalId' => '0000000000.0001'
            ]);


        $this->assertEquals(PhoneCall::STATUS_PROCESSED, $phoneCall->getStatus());
        $this->assertCount(1, $phoneCall->getCallbacks());
        $this->assertEquals(PhoneCall::RESULT_FAIL, $phoneCall->getResult());
        $callback = $phoneCall->getCallbacks()->first();

        $this->assertEquals(Callback::STATUS_FAIL, $callback->getStatus());

        $this->assertInstanceOf(Shoulder::class, $callback->getFirstShoulder());

        /** @var Shoulder $firstShoulder */
        $firstShoulder = $callback->getFirstShoulder();
        $this->assertEquals('79000000004', $firstShoulder->getPhone());
        $this->assertEquals(Shoulder::TARIFF_MOBILE, $firstShoulder->getTariff());
        $this->assertEquals(Shoulder::STATUS_CANCEL, $firstShoulder->getStatus());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568714580'), $firstShoulder->getStartAt());
        $this->assertNull($firstShoulder->getAnswerAt());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568714610'), $firstShoulder->getHangupAt());
    }

    public function testPostCallbackAction_withCase_2()
    {
        $this->prepareTestEnvironment('0000000000.0002');

        $requestData = [
            'event' => 'hangup',
            'call1_phone' => '79000000004',
            'call1_billsec' => '0',
            'call1_tarif' => 'mobile',
            'call1_start_at' => '1568714839',
            'call1_answer_at' => '',
            'call1_hangup_at' => '1568714849',
            'call1_status' => 'busy',
            'recording' => 'some_url',
            'call_id' => '0000000000.0002'
        ];

        $response = $this->makeRequestToAction($requestData);

        $this->assertTrue($response->isSuccessful());

        /** @var PhoneCall $phoneCall */
        $phoneCall = $this->entityManager->getRepository(PhoneCall::class)
            ->findOneBy([
                'externalId' => '0000000000.0002'
            ]);


        $this->assertEquals(PhoneCall::STATUS_PROCESSED, $phoneCall->getStatus());
        $this->assertCount(1, $phoneCall->getCallbacks());
        $this->assertEquals(PhoneCall::RESULT_FAIL, $phoneCall->getResult());
        $callback = $phoneCall->getCallbacks()->first();

        $this->assertEquals(Callback::STATUS_FAIL, $callback->getStatus());

        $this->assertInstanceOf(Shoulder::class, $callback->getFirstShoulder());

        /** @var Shoulder $firstShoulder */
        $firstShoulder = $callback->getFirstShoulder();
        $this->assertEquals('79000000004', $firstShoulder->getPhone());
        $this->assertEquals(Shoulder::TARIFF_MOBILE, $firstShoulder->getTariff());
        $this->assertEquals(Shoulder::STATUS_BUSY, $firstShoulder->getStatus());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568714839'), $firstShoulder->getStartAt());
        $this->assertNull($firstShoulder->getAnswerAt());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568714849'), $firstShoulder->getHangupAt());
    }

    public function testPostCallbackAction_withCase_3()
    {
        $this->prepareTestEnvironment('0000000000.0003');

        $requestData = [
            'event' => 'hangup',
            'call1_phone' => '79000000004',
            'call1_billsec' => '39',
            'call1_tarif' => 'mobile',
            'call1_start_at' => '1568715366',
            'call1_answer_at' => '1568715375',
            'call1_hangup_at' => '1568715414',
            'call1_status' => 'answer',
            'call2_phone' => '79000000003',
            'call2_billsec' => '0',
            'call2_tarif' => 'mobile',
            'call2_start_at' => '1568715375',
            'call2_answer_at' => '1568715413',
            'call2_hangup_at' => '1568715414',
            'call2_status' => 'answer',
            'recording' => 'some_url',
            'call_id' => '0000000000.0003'
        ];

        $response = $this->makeRequestToAction($requestData);

        $this->assertTrue($response->isSuccessful());

        /** @var PhoneCall $phoneCall */
        $phoneCall = $this->entityManager->getRepository(PhoneCall::class)
            ->findOneBy([
                'externalId' => '0000000000.0003'
            ]);


        $this->assertEquals(PhoneCall::STATUS_PROCESSED, $phoneCall->getStatus());
        $this->assertCount(1, $phoneCall->getCallbacks());
        $this->assertEquals(PhoneCall::RESULT_FAIL, $phoneCall->getResult());
        $callback = $phoneCall->getCallbacks()->first();

        $this->assertEquals(Callback::STATUS_FAIL, $callback->getStatus());

        $this->assertInstanceOf(Shoulder::class, $callback->getFirstShoulder());

        /** @var Shoulder $firstShoulder */
        $firstShoulder = $callback->getFirstShoulder();
        $this->assertEquals('79000000004', $firstShoulder->getPhone());
        $this->assertEquals(Shoulder::TARIFF_MOBILE, $firstShoulder->getTariff());
        $this->assertEquals(Shoulder::STATUS_ANSWER, $firstShoulder->getStatus());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568715366'), $firstShoulder->getStartAt());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568715375'), $firstShoulder->getAnswerAt());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568715414'), $firstShoulder->getHangupAt());

        /** @var Shoulder $secondShoulder */
        $secondShoulder = $callback->getSecondShoulder();
        $this->assertEquals('79000000003', $secondShoulder->getPhone());
        $this->assertEquals(Shoulder::TARIFF_MOBILE, $secondShoulder->getTariff());
        $this->assertEquals(Shoulder::STATUS_ANSWER, $secondShoulder->getStatus());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568715375'), $secondShoulder->getStartAt());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568715413'), $secondShoulder->getAnswerAt());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568715414'), $secondShoulder->getHangupAt());
    }

    public function testPostCallbackAction_withCase_4()
    {
        $this->prepareTestEnvironment('0000000000.0004');

        $requestData = [
            'event' => 'hangup',
            'call1_phone' => '79000000004',
            'call1_billsec' => '11',
            'call1_tarif' => 'mobile',
            'call1_start_at' => '1568715664',
            'call1_answer_at' => '1568715672',
            'call1_hangup_at' => '1568715683',
            'call1_status' => 'answer',
            'call2_phone' => '79000000003',
            'call2_billsec' => '3',
            'call2_tarif' => 'mobile',
            'call2_start_at' => '1568715672',
            'call2_answer_at' => '1568715680',
            'call2_hangup_at' => '1568715683',
            'call2_status' => 'answer',
            'recording' => 'some_url',
            'call_id' => '0000000000.0004'
        ];

        $response = $this->makeRequestToAction($requestData);

        $this->assertTrue($response->isSuccessful());

        /** @var PhoneCall $phoneCall */
        $phoneCall = $this->entityManager->getRepository(PhoneCall::class)
            ->findOneBy([
                'externalId' => '0000000000.0004'
            ]);


        $this->assertEquals(PhoneCall::STATUS_PROCESSED, $phoneCall->getStatus());
        $this->assertCount(1, $phoneCall->getCallbacks());
        $this->assertEquals(PhoneCall::RESULT_FAIL, $phoneCall->getResult());
        $callback = $phoneCall->getCallbacks()->first();

        $this->assertEquals(Callback::STATUS_FAIL, $callback->getStatus());

        $this->assertInstanceOf(Shoulder::class, $callback->getFirstShoulder());

        /** @var Shoulder $firstShoulder */
        $firstShoulder = $callback->getFirstShoulder();
        $this->assertEquals('79000000004', $firstShoulder->getPhone());
        $this->assertEquals(Shoulder::TARIFF_MOBILE, $firstShoulder->getTariff());
        $this->assertEquals(Shoulder::STATUS_ANSWER, $firstShoulder->getStatus());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568715664'), $firstShoulder->getStartAt());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568715672'), $firstShoulder->getAnswerAt());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568715683'), $firstShoulder->getHangupAt());

        /** @var Shoulder $secondShoulder */
        $secondShoulder = $callback->getSecondShoulder();
        $this->assertEquals('79000000003', $secondShoulder->getPhone());
        $this->assertEquals(Shoulder::TARIFF_MOBILE, $secondShoulder->getTariff());
        $this->assertEquals(Shoulder::STATUS_ANSWER, $secondShoulder->getStatus());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568715672'), $secondShoulder->getStartAt());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568715680'), $secondShoulder->getAnswerAt());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568715683'), $secondShoulder->getHangupAt());
    }

    public function testPostCallbackAction_withCase_5()
    {
        $this->prepareTestEnvironment('0000000000.0005');

        $requestData = [
            'event' => 'hangup',
            'call1_phone' => '79000000004',
            'call1_billsec' => '24',
            'call1_tarif' => 'mobile',
            'call1_start_at' => '1568780744',
            'call1_answer_at' => '1568780752',
            'call1_hangup_at' => '1568780776',
            'call1_status' => 'answer',
            'call2_phone' => '79000000003',
            'call2_billsec' => '15',
            'call2_tarif' => 'mobile',
            'call2_start_at' => '1568780752',
            'call2_answer_at' => '1568780761',
            'call2_hangup_at' => '1568780776',
            'call2_status' => 'answer',
            'recording' => 'some_url',
            'call_id' => '0000000000.0005'
        ];

        $response = $this->makeRequestToAction($requestData);

        $this->assertTrue($response->isSuccessful());

        /** @var PhoneCall $phoneCall */
        $phoneCall = $this->entityManager->getRepository(PhoneCall::class)
            ->findOneBy([
                'externalId' => '0000000000.0005'
            ]);


        $this->assertEquals(PhoneCall::STATUS_PROCESSED, $phoneCall->getStatus());
        $this->assertCount(1, $phoneCall->getCallbacks());
        $this->assertEquals(PhoneCall::RESULT_SUCCESS, $phoneCall->getResult());
        $callback = $phoneCall->getCallbacks()->first();

        $this->assertEquals(Callback::STATUS_SUCCESS, $callback->getStatus());

        $this->assertInstanceOf(Shoulder::class, $callback->getFirstShoulder());

        /** @var Shoulder $firstShoulder */
        $firstShoulder = $callback->getFirstShoulder();
        $this->assertEquals('79000000004', $firstShoulder->getPhone());
        $this->assertEquals(Shoulder::TARIFF_MOBILE, $firstShoulder->getTariff());
        $this->assertEquals(Shoulder::STATUS_ANSWER, $firstShoulder->getStatus());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568780744'), $firstShoulder->getStartAt());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568780752'), $firstShoulder->getAnswerAt());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568780776'), $firstShoulder->getHangupAt());

        /** @var Shoulder $secondShoulder */
        $secondShoulder = $callback->getSecondShoulder();
        $this->assertEquals('79000000003', $secondShoulder->getPhone());
        $this->assertEquals(Shoulder::TARIFF_MOBILE, $secondShoulder->getTariff());
        $this->assertEquals(Shoulder::STATUS_ANSWER, $secondShoulder->getStatus());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568780752'), $secondShoulder->getStartAt());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568780761'), $secondShoulder->getAnswerAt());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568780776'), $secondShoulder->getHangupAt());
    }

    public function testPostCallbackAction_withCase_6()
    {
        $this->prepareTestEnvironment('0000000000.0006');

        $requestData = [
            'event' => 'hangup',
            'call1_phone' => '79000000004',
            'call1_billsec' => '67',
            'call1_tarif' => 'mobile',
            'call1_start_at' => '1568781338',
            'call1_answer_at' => '1568781348',
            'call1_hangup_at' => '1568781415',
            'call1_status' => 'answer',
            'call2_phone' => '79000000003',
            'call2_billsec' => '60',
            'call2_tarif' => 'mobile',
            'call2_start_at' => '1568781348',
            'call2_answer_at' => '1568781355',
            'call2_hangup_at' => '1568781415',
            'call2_status' => 'answer',
            'recording' => 'some_url',
            'call_id' => '0000000000.0006'
        ];

        $response = $this->makeRequestToAction($requestData);

        $this->assertTrue($response->isSuccessful());

        /** @var PhoneCall $phoneCall */
        $phoneCall = $this->entityManager->getRepository(PhoneCall::class)
            ->findOneBy([
                'externalId' => '0000000000.0006'
            ]);


        $this->assertEquals(PhoneCall::STATUS_PROCESSED, $phoneCall->getStatus());
        $this->assertCount(1, $phoneCall->getCallbacks());
        $this->assertEquals(PhoneCall::RESULT_SUCCESS, $phoneCall->getResult());
        $callback = $phoneCall->getCallbacks()->first();

        $this->assertEquals(Callback::STATUS_SUCCESS, $callback->getStatus());

        $this->assertInstanceOf(Shoulder::class, $callback->getFirstShoulder());

        /** @var Shoulder $firstShoulder */
        $firstShoulder = $callback->getFirstShoulder();
        $this->assertEquals('79000000004', $firstShoulder->getPhone());
        $this->assertEquals(Shoulder::TARIFF_MOBILE, $firstShoulder->getTariff());
        $this->assertEquals(Shoulder::STATUS_ANSWER, $firstShoulder->getStatus());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568781338'), $firstShoulder->getStartAt());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568781348'), $firstShoulder->getAnswerAt());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568781415'), $firstShoulder->getHangupAt());

        /** @var Shoulder $secondShoulder */
        $secondShoulder = $callback->getSecondShoulder();
        $this->assertEquals('79000000003', $secondShoulder->getPhone());
        $this->assertEquals(Shoulder::TARIFF_MOBILE, $secondShoulder->getTariff());
        $this->assertEquals(Shoulder::STATUS_ANSWER, $secondShoulder->getStatus());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568781348'), $secondShoulder->getStartAt());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568781355'), $secondShoulder->getAnswerAt());
        $this->assertEquals(\DateTime::createFromFormat('U', '1568781415'), $secondShoulder->getHangupAt());
    }

    private function prepareTestEnvironment(string $callId): void
    {
        $buyer = $this->createBuyerWithAccountAndCompany();
        $seller = $this->createSeller();
        $lead = $this->createLead($seller);

        $trade = $this->tradeManager->start($buyer, $seller, $lead, 10000, false);

        $phoneCall = $this->phoneCallManager->create($buyer, $trade);
        $phoneCall
            ->setExternalId($callId)
            ->setStatus(PhoneCall::STATUS_REQUESTED);

        $telephonyAccount = new Account();
        $telephonyAccount
            ->setDescription('для телефонии')
            ->setEnabled(true);

        $this->entityManager->persist($telephonyAccount);

        $this->entityManager->flush();

        // Тут чистим UnitOfWork для того чтобы использовалась БД.
        $this->entityManager->clear();
    }

    /**
     * @param array|null $data
     *
     * @return Response
     */
    private function makeRequestToAction(?array $data = []): Response
    {
        $url = $this->router->generate('api_v1_telephony_callback');

        $this->client->request('POST', $url, $data);

        return $this->client->getResponse();
    }

    /**
     * @return User
     */
    private function createBuyerWithAccountAndCompany(): User
    {
        $buyer = new User();
        $buyer
            ->setName('Company 1')
            ->setEmail('company1@xoptov.ru')
            ->setPhone('79000000001')
            ->setPlainPassword(123456)
            ->switchToCompany()
            ->setEnabled(true);

        $this->entityManager->persist($buyer);
        $this->userManager->updateUser($buyer, false);

        $buyerAccount = new ClientAccount();
        $buyerAccount
            ->setUser($buyer)
            ->setBalance(20000)
            ->setEnabled(true);

        $this->entityManager->persist($buyerAccount);
        $buyer->setAccount($buyerAccount);

        $buyerCompany = new Company();
        $buyerCompany
            ->setUser($buyer)
            ->setShortName('ООО Рога и Копыта')
            ->setLargeName('Общество с Ограниченной Ответственностью Рога и Копыта')
            ->setPhone('79000000004')
            ->setInn('123456789098')
            ->setOgrn('123456789098765')
            ->setKpp('123456789')
            ->setBik('123456789')
            ->setAccountNumber('1234567890987654321234567')
            ->setAddress('г.Краснодар, ул.Красная')
            ->setZipcode('123456')
            ->setEmail('roga_i_kopyta@xoptov.ru')
            ->setOfficePhone('79000000004');

        $this->entityManager->persist($buyerCompany);
        $buyer->setCompany($buyerCompany);

        return $buyer;
    }

    /**
     * @return User
     */
    private function createSeller(): User
    {
        $seller = new User();
        $seller
            ->setName('Webmaster 1')
            ->setEmail('webmaster1@xoptov.ru')
            ->setPhone('79000000002')
            ->setPlainPassword(123456)
            ->switchToWebmaster()
            ->setEnabled(true);

        $this->entityManager->persist($seller);
        $this->userManager->updateUser($seller, false);

        return $seller;
    }

    /**
     * @param User $seller
     * @return Lead
     */
    private function createLead(User $seller): Lead
    {
        $lead = new Lead();
        $lead
            ->setPhone('79000000003')
            ->setStatus(Lead::STATUS_EXPECT)
            ->setPrice(10000)
            ->setUser($seller)
            ->setExpirationDate(new \DateTime('+1 day'));

        $this->entityManager->persist($lead);

        return $lead;
    }
}