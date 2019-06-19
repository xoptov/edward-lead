<?php

namespace Unit\AppBundle\Service;

use GuzzleHttp\Client;
use AppBundle\Entity\Lead;
use AppBundle\Entity\User;
use AppBundle\Entity\Company;
use AppBundle\Entity\PhoneCall;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use AppBundle\Entity\MonetaryHold;
use AppBundle\Service\HoldManager;
use AppBundle\Entity\ClientAccount;
use AppBundle\Service\AccountManager;
use AppBundle\Service\PhoneCallManager;

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

        $client = $this->createMock(Client::class);

        $phoneCallManager = new PhoneCallManager($entityManager, $accountManager, $holdManager, $client, '/make.php', $costPerSecond, $firstCallTimeout);

        $result = $phoneCallManager->create($caller, $lead);

        $this->assertInstanceOf(PhoneCall::class, $result);
        $this->assertSame($caller, $result->getCaller());
        $this->assertSame($lead, $result->getLead());
        $this->assertSame($hold, $result->getHold());
    }
}