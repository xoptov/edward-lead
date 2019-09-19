<?php

namespace Tests\unit\AppBundle\Service;

use AppBundle\Entity\Trade;
use AppBundle\Entity\Account;
use AppBundle\Entity\PhoneCall;
use AppBundle\Service\TradeManager;
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
}