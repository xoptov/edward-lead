<?php

namespace Unit\AppBundle\Service;

use AppBundle\Entity\Account;
use AppBundle\Repository\AccountRepository;
use AppBundle\Service\TransactionManager;
use GuzzleHttp\Client;
use AppBundle\Entity\Lead;
use AppBundle\Entity\User;
use AppBundle\Entity\Company;
use AppBundle\Entity\PhoneCall;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use AppBundle\Entity\MonetaryHold;
use AppBundle\Service\HoldManager;
use AppBundle\Entity\ClientAccount;
use AppBundle\Service\AccountManager;
use AppBundle\Service\PhoneCallManager;
use Symfony\Component\HttpFoundation\Request;

class PhoneCallManagerTest extends TestCase
{
    public function testCreate_withSuccess()
    {
        $company = new Company();
        $company->setOfficePhone('101');

        $callerAvailableBalance = 200000;

        $account = new ClientAccount();
        $account
            ->setBalance($callerAvailableBalance)
            ->setEnabled(1);

        $caller = new User();
        $caller
            ->setAccount($account)
            ->setCompany($company)
            ->addRole('ROLE_COMPANY');

        $lead = new Lead();

        $firstCallTimeout = 300; // сукунды
        $costPerSecond = 350; // копейки

        $holdAmount = $firstCallTimeout * $costPerSecond;

        $phoneCall = new PhoneCall();
        $phoneCall
            ->setCaller($caller)
            ->setLead($lead);

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
            ->willReturn($callerAvailableBalance);

        $holdManager = $this->createMock(HoldManager::class);
        $holdManager
            ->expects($this->once())
            ->method('create')
            ->with($account, $phoneCall, $holdAmount)
            ->willReturn($hold);

        $transactionManager = $this->createMock(TransactionManager::class);

        $client = $this->createMock(Client::class);

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

        $result = $phoneCallManager->create($caller, $lead);

        $this->assertInstanceOf(PhoneCall::class, $result);
        $this->assertSame($caller, $result->getCaller());
        $this->assertSame($lead, $result->getLead());
        $this->assertSame($hold, $result->getHold());
    }

    public function testRequestConnect_withSuccess()
    {
        $officePhone = '101';

        $company = new Company();
        $company->setOfficePhone($officePhone);

        $caller = new User();
        $caller->setCompany($company);

        $leadPhone = '102';

        $lead = new Lead();
        $lead->setPhone($leadPhone);

        $phoneCall = new PhoneCall();
        $phoneCall
            ->setCaller($caller)
            ->setLead($lead);

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
                'timeout' => $firstCallTimeout
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