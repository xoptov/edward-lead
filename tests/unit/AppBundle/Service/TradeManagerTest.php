<?php

namespace Tests\unit\AppBundle\Service;

use AppBundle\Entity\Trade;
use AppBundle\Entity\Account;
use AppBundle\Entity\PhoneCall;
use AppBundle\Entity\User;
use AppBundle\Service\AccountManager;
use AppBundle\Service\FeesManager;
use AppBundle\Service\HoldManager;
use AppBundle\Service\ReferrerRewardManager;
use AppBundle\Service\TradeManager;
use AppBundle\Service\TransactionManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class TradeManagerTest extends TestCase
{
    public function testAutoFinish_withStaleTradeWithoutPhoneCall()
    {
        $createdAt = new \DateTime('-50 hours');

        $trade = new Trade();
        $trade->setCreatedAt($createdAt);

        $feesAccount = new Account();

        $tradeManager = $this->createPartialMock(TradeManager::class, ['accept', 'reject']);

        $tradeManager
            ->expects($this->once())
            ->method('accept')
            ->with($trade, $feesAccount);

        $staleTimeBound = new \DateTime('-48 hours');

        /** @var TradeManager $tradeManager */
        $tradeManager->autoFinish($trade, $feesAccount, $staleTimeBound);
    }

    public function testAutoFinish_withStaleTradeAndPhoneCall()
    {
        $createdAt = new \DateTime('-50 hours');

        $phoneCall = new PhoneCall();

        $trade = new Trade();
        $trade->setCreatedAt($createdAt)->addPhoneCall($phoneCall);


        $feesAccount = new Account();

        $tradeManager = $this->createPartialMock(TradeManager::class, ['accept', 'reject']);

        $tradeManager
            ->expects($this->once())
            ->method('reject')
            ->with($trade);

        $staleTimeBound = new \DateTime('-48 hours');

        /** @var TradeManager $tradeManager */
        $tradeManager->autoFinish($trade, $feesAccount, $staleTimeBound);
    }

    public function testAutoFinish_withStalePhoneCall()
    {
        $createdAt = new \DateTime('-50 hours');

        $phoneCall = new PhoneCall();
        $phoneCall
            ->setResult(PhoneCall::RESULT_SUCCESS)
            ->setCreatedAt($createdAt);

        $trade = new Trade();
        $trade
            ->setStatus(Trade::STATUS_CALL_BACK)
            ->addPhoneCall($phoneCall);

        $feesAccount = new Account();

        $tradeManager = $this->createPartialMock(TradeManager::class, ['accept', 'reject']);

        $tradeManager
            ->expects($this->once())
            ->method('accept')
            ->with($trade, $feesAccount);

        $staleTimeBound = new \DateTime('-48 hours');

        /** @var TradeManager $tradeManager */
        $tradeManager->autoFinish($trade, $feesAccount, $staleTimeBound);
    }

    public function testAutoFinish_with2AsksCallback()
    {
        $phoneCalls1createdAt = new \DateTime('-40 hours');
        $phoneCalls2createdAt = new \DateTime('-35 hours');

        $phoneCall1 = new PhoneCall();
        $phoneCall1
            ->setResult(PhoneCall::RESULT_SUCCESS)
            ->setCreatedAt($phoneCalls1createdAt);

        $phoneCall2 = new PhoneCall();
        $phoneCall2
            ->setResult(PhoneCall::RESULT_SUCCESS)
            ->setCreatedAt($phoneCalls2createdAt);

        $trade = new Trade();
        $trade->setStatus(Trade::STATUS_CALL_BACK);
        $trade->addPhoneCall($phoneCall1);
        $trade->addPhoneCall($phoneCall2);
        $trade->addAskCallbackPhoneCall($phoneCall1);
        $trade->addAskCallbackPhoneCall($phoneCall2);

        $feesAccount = new Account();

        $tradeManager = $this->createPartialMock(TradeManager::class, ['accept', 'reject']);

        $tradeManager
            ->expects($this->once())
            ->method('reject')
            ->with($trade);

        $staleTimeBound = new \DateTime('-48 hours');

        /** @var TradeManager $tradeManager */
        $tradeManager->autoFinish($trade, $feesAccount, $staleTimeBound);
    }

    public function testAutoFinish_withStaleSuccessTrade()
    {
        $createdAt = new \DateTime('-50 hours');

        $phoneCall = new PhoneCall();
        $phoneCall
            ->setResult(PhoneCall::RESULT_SUCCESS)
            ->setCreatedAt($createdAt);

        $trade = new Trade();
        $trade->addPhoneCall($phoneCall);

        $feesAccount = new Account();

        $tradeManager = $this->createPartialMock(TradeManager::class, ['accept', 'reject']);

        $tradeManager
            ->expects($this->once())
            ->method('accept')
            ->with($trade, $feesAccount);

        $staleTimeBound = new \DateTime('-48 hours');

        /** @var TradeManager $tradeManager */
        $tradeManager->autoFinish($trade, $feesAccount, $staleTimeBound);
    }

    public function testIsCanShowResultModal_withTradeStatusNew_withoutPhoneCall()
    {
        $trade = new Trade();

        $tradeManager = $this->createTradeManager();

        $this->assertFalse($tradeManager->isCanShowResultModal($trade));
    }

    public function testIsCanShowResultModal_withTradeStatusNew_andPhoneCallResultSuccess()
    {
        $trade = new Trade();

        $phoneCall = new PhoneCall();
        $phoneCall->setResult(PhoneCall::RESULT_SUCCESS);

        $trade->addPhoneCall($phoneCall);

        $tradeManager = $this->createTradeManager();

        $this->assertTrue($tradeManager->isCanShowResultModal($trade));
    }

    public function testIsCanShowResultModal_withTradeStatusNew_andPhoneCallResultFail()
    {
        $trade = new Trade();

        $phoneCall = new PhoneCall();
        $phoneCall->setResult(PhoneCall::RESULT_FAIL);

        $trade->addPhoneCall($phoneCall);

        $tradeManager = $this->createTradeManager();

        $this->assertFalse($tradeManager->isCanShowResultModal($trade));
    }

    public function testIsCanShowResultModal_withTradeStatusCallback_andLastPhoneCallSuccess_andResulted()
    {
        $trade = new Trade();
        $trade->setStatus(Trade::STATUS_CALL_BACK);

        $phoneCall = new PhoneCall();
        $phoneCall->setResult(PhoneCall::RESULT_SUCCESS);

        $trade->addPhoneCall($phoneCall);
        $trade->addAskCallbackPhoneCall($phoneCall);

        $tradeManager = $this->createTradeManager();

        $this->assertFalse($tradeManager->isCanShowResultModal($trade));
    }

    public function testIsCanShowResultModal_withTradeStatusCallback_andLastPhoneCallSuccess_andNotResulted()
    {
        $trade = new Trade();
        $trade->setStatus(Trade::STATUS_CALL_BACK);

        $phoneCall = new PhoneCall();
        $phoneCall->setResult(PhoneCall::RESULT_SUCCESS);

        $trade->addPhoneCall($phoneCall);

        $tradeManager = $this->createTradeManager();

        $this->assertTrue($tradeManager->isCanShowResultModal($trade));
    }

    /**
     * @return TradeManager
     *
     * @throws \ReflectionException
     */
    private function createTradeManager(): TradeManager
    {
        /** @var LoggerInterface $logger */
        $logger = $this->createMock(LoggerInterface::class);

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->createMock(EntityManagerInterface::class);

        /** @var AccountManager $accountManager */
        $accountManager = $this->createMock(AccountManager::class);

        /** @var HoldManager $holdManager */
        $holdManager = $this->createMock(HoldManager::class);

        /** @var FeesManager $feesManager */
        $feesManager = $this->createMock(FeesManager::class);

        /** @var TransactionManager $transactionManager */
        $transactionManager = $this->createMock(TransactionManager::class);

        /** @var ReferrerRewardManager $referrerRewardManager */
        $referrerRewardManager = $this->createMock(ReferrerRewardManager::class);

        return new TradeManager(
            $logger,
            $entityManager,
            $accountManager,
            $holdManager,
            $feesManager,
            $referrerRewardManager,
            $transactionManager,
            2
        );
    }
}