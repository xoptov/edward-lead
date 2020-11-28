<?php

namespace AppBundle\Service;

use GuzzleHttp\Client;
use AppBundle\Entity\Lead;
use AppBundle\Entity\User;
use AppBundle\Entity\Trade;
use AppBundle\Entity\Account;
use AppBundle\Entity\PhoneCall;
use AppBundle\Entity\PBX\Callback;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\GuzzleException;
use AppBundle\Exception\AccountException;
use Doctrine\ORM\NonUniqueResultException;
use AppBundle\Exception\PhoneCallException;
use AppBundle\Exception\OperationException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Exception\RequestCallException;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Exception\InsufficientFundsException;

class PhoneCallManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var AccountManager
     */
    private $accountManager;

    /**
     * @var HoldManager
     */
    private $holdManager;

    /**
     * @var TransactionManager
     */
    private $transactionManager;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $pbxCallUrl;

    /**
     * @var float
     */
    private $costPerSecond;

    /**
     * @var int
     */
    private $talkTimeout;

    /**
     * @var int
     */
    private $hangupTimeout;

    /**
     * @var int
     */
    private $maxAsksCallback;

    /**
     * @var bool
     */
    private $telephonyEnabled;

    /**
     * @param EntityManagerInterface $entityManager
     * @param AccountManager         $accountManager
     * @param HoldManager            $holdManager
     * @param TransactionManager     $transactionManager
     * @param Client                 $client
     * @param string                 $pbxCallUrl
     * @param float                  $costPerSecond
     * @param int                    $talkTimeout
     * @param int                    $hangupTimeout
     * @param int                    $maxAsksCallback
     * @param bool                   $telephonyEnabled
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        AccountManager $accountManager,
        HoldManager $holdManager,
        TransactionManager $transactionManager,
        Client $client,
        string $pbxCallUrl,
        float $costPerSecond,
        int $talkTimeout,
        int $hangupTimeout,
        int $maxAsksCallback,
        bool $telephonyEnabled
    ) {
        $this->entityManager = $entityManager;
        $this->accountManager = $accountManager;
        $this->holdManager = $holdManager;
        $this->transactionManager = $transactionManager;
        $this->client = $client;
        $this->pbxCallUrl = $pbxCallUrl;
        $this->costPerSecond = $costPerSecond;
        $this->talkTimeout = $talkTimeout;
        $this->hangupTimeout = $hangupTimeout;
        $this->maxAsksCallback = $maxAsksCallback;
        $this->telephonyEnabled = $telephonyEnabled;
    }

    /**
     * @param User  $caller
     * @param Trade $trade
     *
     * @return PhoneCall
     *
     * @throws RequestCallException
     * @throws InsufficientFundsException
     */
    public function create(User $caller, Trade $trade): PhoneCall
    {
        if (!$caller->isAdvertiser()) {
            throw new RequestCallException($caller, $trade, 'Пользователь должен быть рекламодателем');
        }

        $officePhone = $caller->getOfficePhone();

        if (!$officePhone) {
            throw new RequestCallException($caller, $trade, 'Для совершения звонка лиду необходимо указать номер телефона офиса в профиле компании');
        }

        $callCost = $this->calculateCallCost($this->calculateMinTalkSec());

        if (!$this->isEnoughMoney($caller)) {
            throw new InsufficientFundsException($caller->getAccount(), $callCost, 'Недостаточно средств для совершения звонка');
        }

        $phoneCall = new PhoneCall();
        $phoneCall
            ->setCaller($caller)
            ->setTrade($trade)
            ->setDescription('Звонок лиду');

        $this->entityManager->persist($phoneCall);

        $hold = $this->holdManager->create($caller->getAccount(), $phoneCall, $callCost, false);
        $phoneCall->setHold($hold);

        return $phoneCall;
    }

    /**
     * @param PhoneCall $phoneCall
     * @param bool      $flush
     *
     * @throws OperationException
     */
    public function requestConnection(
        PhoneCall $phoneCall,
        bool $flush = true
    ): void {

        try {

            $response = $this->client->request(Request::METHOD_GET, $this->pbxCallUrl, [
                'query' => [
                    'ext' => $phoneCall->getCallerPhone(),
                    'num' => $phoneCall->getLeadPhone(),
                    'dur' => $this->talkTimeout
                ]
            ]);

            if ($response->getStatusCode() !== Response::HTTP_OK) {
                $this->holdManager->remove($phoneCall);
                throw new OperationException($phoneCall, 'Ошибка запроса соединения');
            }

        } catch (GuzzleException $e) {
            $this->holdManager->remove($phoneCall);
            throw new OperationException($phoneCall, 'Ошибка запроса соединения');
        }

        $json = json_decode($response->getBody(), true);

        if (!isset($json['call_id'])) {
            $this->holdManager->remove($phoneCall);
            throw new OperationException($phoneCall, 'Ошибка. Не указан идентификатор вызова');
        }

        $phoneCall
            ->setExternalId($json['call_id'])
            ->setStatus(PhoneCall::STATUS_REQUESTED);

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param PhoneCall $phoneCall
     * @param Callback  $pbxCallback
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws AccountException
     * @throws PhoneCallException
     */
    public function process(PhoneCall $phoneCall, Callback $pbxCallback): void
    {
        if ($phoneCall->isProcessed()) {
            throw new PhoneCallException($phoneCall, 'Телефонный звонок уже обработан');
        }

        $callCost = $this->calculateCallCost($pbxCallback->getTotalBillSec());
        $phoneCall->setAmount($callCost);

        if ($pbxCallback->isSuccess()) {
            $this->processSuccessfulPhoneCall($phoneCall);
        } else {
            $this->processFailedPhoneCall($phoneCall);
        }
    }

    /**
     * @param PhoneCall $phoneCall
     *
     * @throws AccountException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function processSuccessfulPhoneCall(PhoneCall $phoneCall): void
    {
        $caller  = $phoneCall->getCaller();

        $telephonyAccount = $this->entityManager
            ->getRepository(Account::class)
            ->getTelephonyAccount();

        $transactions = $this->transactionManager->create(
            $caller->getAccount(),
            $telephonyAccount,
            $phoneCall,
            false
        );

        $this->entityManager->transactional(function(EntityManagerInterface $em) use ($phoneCall, $transactions) {

            if (!empty($transactions)) {
                $this->transactionManager->process($transactions);
            }

            $phoneCall
                ->setResult(PhoneCall::RESULT_SUCCESS)
                ->setStatus(PhoneCall::STATUS_PROCESSED);

            $this->holdManager->remove($phoneCall, false);

            $em->flush();
        });
    }

    /**
     * @param PhoneCall $phoneCall
     *
     * @throws AccountException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function processFailedPhoneCall(PhoneCall $phoneCall): void
    {
        $caller  = $phoneCall->getCaller();

        $telephonyAccount = $this->entityManager
            ->getRepository(Account::class)
            ->getTelephonyAccount();

        $transactions = $this->transactionManager->create(
            $caller->getAccount(),
            $telephonyAccount,
            $phoneCall,
            false
        );

        $this->entityManager->transactional(function(EntityManagerInterface $em) use ($phoneCall, $transactions) {

            if (!empty($transactions)) {
                $this->transactionManager->process($transactions);
            }

            $phoneCall
                ->setResult(PhoneCall::RESULT_FAIL)
                ->setStatus(PhoneCall::STATUS_PROCESSED);

            $this->holdManager->remove($phoneCall, false);

            $em->flush();
        });
    }

    /**
     * @param User $caller
     *
     * @return bool
     */
    public function isEnoughMoney(User $caller): bool
    {
        $callCost = $this->calculateCallCost($this->calculateMinTalkSec());
        $callerBalance = $this->accountManager->getAvailableBalance($caller->getAccount());

        if ($callCost <= $callerBalance) {
            return true;
        }

        return false;
    }

    /**
     * @param User $caller
     * @param Lead $lead
     *
     * @return bool
     */
    public function isCanMakeCall(User $caller, Lead $lead): bool
    {
        if (!$this->telephonyEnabled) {
            return false;
        }

        $trade = $lead->getTrade();

        if (empty($trade)) {
            return false;
        }

        if ($trade->isProcessed()) {
            return false;
        }

        if (!$trade->isBuyer($caller)) {
            return false;
        }

        if ($lead->hasRoom() && !$lead->isPlatformWarranty()) {
            return false;
        }

        $lastPhoneCall = $trade->getLastPhoneCall();

        if ($trade->isNew()) {
            if (!$lastPhoneCall
                || $lastPhoneCall->isResultFail()
                || ($lastPhoneCall->isEmptyResult() && $lastPhoneCall->isRequested())
            ) {
                return true;
            }
        } elseif ($trade->isCallback() && $lastPhoneCall) {
            if ($lastPhoneCall->isResultFail()
                || ($trade->hasAskCallbackPhoneCall($lastPhoneCall)
                    && $trade->getAskCallbackCount() <= $this->maxAsksCallback)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return int
     */
    private function calculateMinTalkSec(): int
    {
        return $this->talkTimeout * 2 + $this->hangupTimeout;
    }

    /**
     * @param int $seconds
     *
     * @return int
     */
    private function calculateCallCost(int $seconds): int
    {
        return (int)ceil($seconds * $this->costPerSecond);
    }
}
