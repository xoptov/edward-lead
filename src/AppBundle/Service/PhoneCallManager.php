<?php

namespace AppBundle\Service;

use AppBundle\Entity\Account;
use AppBundle\Exception\AccountException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use GuzzleHttp\Client;
use AppBundle\Entity\User;
use AppBundle\Entity\Lead;
use AppBundle\Entity\PhoneCall;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\GuzzleException;
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
     * @param Client                 $client
     * @param string                 $pbxCallUrl
     * @param int                    $costPerSecond
     * @param int                    $firstCallTimeout
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        AccountManager $accountManager,
        HoldManager $holdManager,
        Client $client,
        string $pbxCallUrl,
        int $costPerSecond,
        int $firstCallTimeout
    ) {
        $this->entityManager = $entityManager;
        $this->accountManager = $accountManager;
        $this->holdManager = $holdManager;
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
            ->setLead($lead);
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
                    'timeout' => $this->firstCallTimeout
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

        $phoneCall->setExternalId($json['call_id'])
            ->setStatus(PhoneCall::STATUS_REQUESTED);

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param PhoneCall $phoneCall
     * @param array     $data
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws AccountException
     */
    public function process(PhoneCall $phoneCall, array $data): void
    {
        $reflection = new \ReflectionObject($phoneCall);
        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            if (!isset($data[$propertyName])) {
                continue;
            }
            $property->setAccessible(true);
            if ($property->getValue($phoneCall) && 'status' !== $propertyName) {
                continue;
            }
            $property->setValue($phoneCall, $data[$propertyName]);
        }

        if ($phoneCall->getStatus() === PhoneCall::STATUS_ANSWER) {
            $caller  = $phoneCall->getCaller();
            $telephonyAccount = $this->entityManager
                ->getRepository(Account::class)
                ->getTelephonyAccount();

            $transactions = $this->transactionManager->create($caller->getAccount(), $telephonyAccount, $phoneCall, false);

            $this->entityManager->transactional(function(EntityManagerInterface $em) use ($phoneCall, $transactions) {
                $this->transactionManager->process($transactions);

                if ($phoneCall->hasHold()) {
                    $hold = $phoneCall->getHold();
                    $phoneCall->setHold(null);
                    $em->remove($hold);
                }

                $em->flush();
            });
        } elseif ($phoneCall->hasHold()) {
            $hold = $phoneCall->getHold();
            $phoneCall->setHold(null);

            $this->entityManager->remove($hold);
            $this->entityManager->flush();
        }
    }
}