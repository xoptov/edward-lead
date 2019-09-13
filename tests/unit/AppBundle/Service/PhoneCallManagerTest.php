<?php

namespace Unit\AppBundle\Service;

use GuzzleHttp\Client;
use AppBundle\Entity\Lead;
use AppBundle\Entity\User;
use AppBundle\Entity\Trade;
use AppBundle\Entity\Company;
use GuzzleHttp\Psr7\Response;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\PhoneCall;
use PHPUnit\Framework\TestCase;
use AppBundle\Entity\MonetaryHold;
use AppBundle\Service\HoldManager;
use AppBundle\Entity\ClientAccount;
use AppBundle\Service\AccountManager;
use AppBundle\Service\PhoneCallManager;
use AppBundle\Service\TransactionManager;
use Symfony\Component\HttpFoundation\Request;

class PhoneCallManagerTest extends TestCase
{
    public function testCreate_withSuccess()
    {
        $company = new Company();
        $company->setOfficePhone('101');

        $buyerAvailableBalance = 200000;

        $account = new ClientAccount();
        $account
            ->setBalance($buyerAvailableBalance)
            ->setEnabled(1);

        $buyer = new User();
        $buyer
            ->setAccount($account)
            ->setCompany($company)
            ->addRole('ROLE_COMPANY');


        $seller = new User();

        $lead = new Lead();
        $lead->setUser($seller);

        $trade = new Trade();
        $trade
            ->setLead($lead)
            ->setBuyer($buyer)
            ->setSeller($seller);

        $phoneCall = new PhoneCall();
        $phoneCall
            ->setCaller($buyer)
            ->setTrade($trade);

        $firstCallTimeout = 300; // сукунды
        $costPerSecond = 350; // копейки

        $holdAmount = $firstCallTimeout * $costPerSecond;

        $hold = new MonetaryHold();
        $hold
            ->setAccount($account)
            ->setOperation($phoneCall)
            ->setAmount($holdAmount);

        $entityManager = $this->createMock(EntityManager::class);
        $entityManager
            ->expects($this->once())
            ->method('persist');
        $entityManager
            ->expects($this->once())
            ->method('flush');

        $accountManager = $this->createMock(AccountManager::class);
        $accountManager
            ->expects($this->once())
            ->method('getAvailableBalance')
            ->with($account)
            ->willReturn($buyerAvailableBalance);

        $holdManager = $this->createMock(HoldManager::class);
        $holdManager
            ->expects($this->once())
            ->method('create')
            ->withAnyParameters()
            ->willReturn($hold);

        $transactionManager = $this->createMock(TransactionManager::class);

        $client = $this->createMock(Client::class);

        /**
         * @var EntityManager      $entityManager
         * @var AccountManager     $accountManager
         * @var HoldManager        $holdManager
         * @var TransactionManager $transactionManager
         * @var Client             $client
         */
        $phoneCallManager = new PhoneCallManager(
            $entityManager,
            $accountManager,
            $holdManager,
            $transactionManager,
            $client,
            '/make.php',
            $costPerSecond,
            $firstCallTimeout
        );

        $result = $phoneCallManager->create($buyer, $trade);

        $this->assertInstanceOf(PhoneCall::class, $result);
        $this->assertSame($buyer, $result->getCaller());
        $this->assertSame($trade, $result->getTrade());
        $this->assertSame($hold, $result->getHold());
    }

    public function testRequestConnect_withSuccess()
    {
        $officePhone = '101';

        $company = new Company();
        $company->setOfficePhone($officePhone);

        $buyer = new User();
        $buyer->setCompany($company);

        $leadPhone = '102';

        $lead = new Lead();
        $lead->setPhone($leadPhone);

        $seller = new User();

        $trade = new Trade();
        $trade
            ->setBuyer($buyer)
            ->setSeller($seller)
            ->setLead($lead);

        $phoneCall = new PhoneCall();
        $phoneCall
            ->setCaller($buyer)
            ->setTrade($trade);

        $entityManager = $this->createMock(EntityManager::class);
        $entityManager->expects($this->once())
            ->method('flush');

        $accountManager = $this->createMock(AccountManager::class);
        $holdManager = $this->createMock(HoldManager::class);
        $transactionManager = $this->createMock(TransactionManager::class);

        $pbxCallUrl = '/api/make-call';
        $firstCallTimeout = 300;

        $requestOptions = [
            'query' => [
                'ext' => $officePhone,
                'num' => $leadPhone,
                'dur' => $firstCallTimeout
            ]
        ];

        $callId = '0000000000.001';

        $json = json_encode([
            'call_id' => $callId
        ]);

        $response = $this->createMock(Response::class);
        $response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);
        $response->expects($this->once())
            ->method('getBody')
            ->willReturn($json);

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('request')
            ->with(Request::METHOD_GET, $pbxCallUrl, $requestOptions)
            ->willReturn($response);

        $costPerSecond = 350;

        /**
         * @var EntityManager      $entityManager
         * @var AccountManager     $accountManager
         * @var HoldManager        $holdManager
         * @var TransactionManager $transactionManager
         * @var Client             $client
         */
        $phoneCallManager = new PhoneCallManager(
            $entityManager,
            $accountManager,
            $holdManager,
            $transactionManager,
            $client,
            $pbxCallUrl,
            $costPerSecond,
            $firstCallTimeout
        );

        $phoneCallManager->requestConnection($phoneCall);

        $this->assertEquals(PhoneCall::STATUS_REQUESTED, $phoneCall->getStatus());
        $this->assertEquals($callId, $phoneCall->getExternalId());
    }
}