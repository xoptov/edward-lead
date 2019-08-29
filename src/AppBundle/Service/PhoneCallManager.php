<?php

namespace AppBundle\Service;

use GuzzleHttp\Client;
use AppBundle\Entity\User;
use AppBundle\Entity\Lead;
use AppBundle\Entity\Account;
use AppBundle\Entity\PhoneCall;
use AppBundle\Entity\PBXCallback;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\GuzzleException;
use AppBundle\Exception\AccountException;
use Doctrine\ORM\NonUniqueResultException;
use AppBundle\Exception\PhoneCallException;
use AppBundle\Exception\OperationException;
use Symfony\Component\HttpFoundation\Request;
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
     * @param Lead      $lead
     * @param bool|null $flush
     *
     * @return PhoneCall
     *
     * @throws PhoneCallException
     * @throws InsufficientFundsException
     */
    public function create(User $caller, Lead $lead, ?bool $flush = true): PhoneCall
    {
        $company = $caller->getCompany();

        if (!$company) {
            throw new PhoneCallException($caller, $lead, 'Пользователь должен быть представителем компании');
        }

        $officePhone = $company->getOfficePhone();

        if (!$officePhone) {
            throw new PhoneCallException($caller, $lead, 'Для совершения звонка лиду необходимо указать номер телефона офиса в профиле компании');
        }

        $holdCallCost = $this->firstCallTimeout * $this->costPerSecond;
        $callerBalance = $this->accountManager->getAvailableBalance($caller->getAccount());

        if ($holdCallCost > $callerBalance) {
            throw new InsufficientFundsException($caller->getAccount(), $holdCallCost, 'Недостаточно средств для совершения звонка');
        }

        $phoneCall = new PhoneCall();
        $phoneCall
            ->setCaller($caller)
            ->setLead($lead)
            ->setDescription('Звонок лиду');

        $this->entityManager->persist($phoneCall);

        $hold = $this->holdManager->create($caller->getAccount(), $phoneCall, $holdCallCost, false);
        $phoneCall->setHold($hold);

        if ($flush) {
            $this->entityManager->flush();
        }

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
     * @param PBXCallback $pbxCallback
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws AccountException
     */
    public function process(PBXCallback $pbxCallback): void
    {
        if ($pbxCallback->hasPhoneCall()) {
            $phoneCall = $pbxCallback->getPhoneCall();

            if ($pbxCallback->isAnswered()) {
                $this->processAnsweredPhoneCall($phoneCall);
            } else {
                $this->processOtherPhoneCall($phoneCall);
            }
        } else {
            $this->entityManager->flush();
        }
    }

    /**
     * @param PhoneCall $phoneCall
     *
     * @throws AccountException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function processAnsweredPhoneCall(PhoneCall $phoneCall): void
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

            $phoneCall->setStatus(PhoneCall::STATUS_PROCESSED);

            $em->flush();
        });
    }

    /**
     * @param PhoneCall $phoneCall
     */
    public function processOtherPhoneCall(PhoneCall $phoneCall): void
    {
        if ($phoneCall->hasHold()) {
            $hold = $phoneCall->takeHold();
            $this->entityManager->remove($hold);
        }

        $phoneCall->setStatus(PhoneCall::STATUS_PROCESSED);

        $this->entityManager->flush();
    }
}