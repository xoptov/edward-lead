<?php

namespace AppBundle\Service;

use AppBundle\Exception\PhoneCallException;
use GuzzleHttp\Client;
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
     * @var int
     */
    private $costPerSecond;

    /**
     * @var int
     */
    private $firstCallTimeout;

    /**
     * @param EntityManagerInterface $entityManager
     * @param AccountManager         $accountManager
     * @param HoldManager            $holdManager
     * @param TransactionManager     $transactionManager
     * @param Client                 $client
     * @param string                 $pbxCallUrl
     * @param int                    $costPerSecond
     * @param int                    $firstCallTimeout
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        AccountManager $accountManager,
        HoldManager $holdManager,
        TransactionManager $transactionManager,
        Client $client,
        string $pbxCallUrl,
        int $costPerSecond,
        int $firstCallTimeout
    ) {
        $this->entityManager = $entityManager;
        $this->accountManager = $accountManager;
        $this->holdManager = $holdManager;
        $this->transactionManager = $transactionManager;
        $this->client = $client;
        $this->pbxCallUrl = $pbxCallUrl;
        $this->costPerSecond = $costPerSecond;
        $this->firstCallTimeout = $firstCallTimeout;
    }

    /**
     * @param User      $caller
     * @param Trade     $trade
     *
     * @return PhoneCall
     *
     * @throws RequestCallException
     * @throws InsufficientFundsException
     */
    public function create(User $caller, Trade $trade): PhoneCall
    {
        $company = $caller->getCompany();

        if (!$company) {
            throw new RequestCallException($caller, $trade, 'Пользователь должен быть представителем компании');
        }

        $officePhone = $company->getOfficePhone();

        if (!$officePhone) {
            throw new RequestCallException($caller, $trade, 'Для совершения звонка лиду необходимо указать номер телефона офиса в профиле компании');
        }

        $holdCallCost = $this->firstCallTimeout * $this->costPerSecond;
        $callerBalance = $this->accountManager->getAvailableBalance($caller->getAccount());

        if ($holdCallCost > $callerBalance) {
            throw new InsufficientFundsException($caller->getAccount(), $holdCallCost, 'Недостаточно средств для совершения звонка');
        }

        $phoneCall = new PhoneCall();
        $phoneCall
            ->setCaller($caller)
            ->setTrade($trade)
            ->setDescription('Звонок лиду');

        $this->entityManager->persist($phoneCall);

        $hold = $this->holdManager->create($caller->getAccount(), $phoneCall, $holdCallCost, false);
        $phoneCall->setHold($hold);

        return $phoneCall;
    }

    /**
     * @param PhoneCall $phoneCall
     * @param bool|null $flush
     *
     * @throws OperationException
     */
    public function requestConnection(PhoneCall $phoneCall, ?bool $flush = true): void
    {
        try {
            $response = $this->client->request(Request::METHOD_GET, $this->pbxCallUrl, [
                'query' => [
                    'ext' => $phoneCall->getCallerPhone(),
                    'num' => $phoneCall->getLeadPhone(),
                    'dur' => $this->firstCallTimeout
                ]
            ]);
            if ($response->getStatusCode() !== Response::HTTP_OK) {
                throw new OperationException($phoneCall, 'Ошибка запроса соединения');
            }
        } catch (GuzzleException $e) {
            throw new OperationException($phoneCall, 'Ошибка запроса соединения');
        }

        $json = json_decode($response->getBody(), true);

        if (!isset($json['call_id'])) {
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
    private function processSuccessfulPhoneCall(PhoneCall $phoneCall): void
    {
        $caller  = $phoneCall->getCaller();

        $telephonyAccount = $this->entityManager
            ->getRepository(Account::class)
            ->getTelephonyAccount();

        $transactions = $this->transactionManager->create($caller->getAccount(), $telephonyAccount, $phoneCall, false);

        $this->entityManager->transactional(function(EntityManagerInterface $em) use ($phoneCall, $transactions) {

            if (!empty($transactions)) {
                $this->transactionManager->process($transactions);
            }

            if ($phoneCall->hasHold()) {
                $hold = $phoneCall->takeHold();
                $em->remove($hold);
            }

            $phoneCall->setResult(PhoneCall::RESULT_SUCCESS);
            $phoneCall->setStatus(PhoneCall::STATUS_PROCESSED);

            $em->flush();
        });
    }

    /**
     * @param PhoneCall $phoneCall
     */
    private function processFailedPhoneCall(PhoneCall $phoneCall): void
    {
        if ($phoneCall->hasHold()) {
            $hold = $phoneCall->takeHold();
            $this->entityManager->remove($hold);
        }

        $phoneCall->setResult(PhoneCall::RESULT_FAIL);
        $phoneCall->setStatus(PhoneCall::STATUS_PROCESSED);

        $this->entityManager->flush();
    }
}