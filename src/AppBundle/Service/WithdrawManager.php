<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Entity\Withdraw;
use AppBundle\Entity\OutgoingAccount;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Exception\FinancialException;
use AppBundle\Exception\InsufficientFundsException;

class WithdrawManager
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
     * @param EntityManagerInterface $entityManager
     * @param AccountManager         $accountManager
     * @param HoldManager            $holdManager
     * @param TransactionManager     $transactionManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        AccountManager $accountManager,
        HoldManager $holdManager,
        TransactionManager $transactionManager
    ) {
        $this->entityManager = $entityManager;
        $this->accountManager = $accountManager;
        $this->holdManager = $holdManager;
        $this->transactionManager = $transactionManager;
    }

    /**
     * @param User      $user
     * @param int       $amount
     * @param bool|null $flush
     *
     * @return Withdraw
     *
     * @throws FinancialException
     */
    public function create(User $user, int $amount, ?bool $flush = true): Withdraw
    {
        $balance = $this->accountManager->getAvailableBalance($user->getAccount());

        if ($balance < $amount) {
            throw new FinancialException('Недостаточно средств для вывода');
        }

        $withdraw = new Withdraw();
        $withdraw
            ->setUser($user)
            ->setDescription('Вывод средств с баланса')
            ->setAmount($amount);

        $this->entityManager->persist($withdraw);

        $hold = $this->holdManager->create($user->getAccount(), $withdraw, $amount, false);
        $withdraw->setHold($hold);

        if ($flush) {
            $this->entityManager->flush();
        }

        return $withdraw;
    }

    /**
     * @param Withdraw        $withdraw
     * @param OutgoingAccount $account
     *
     * @throws FinancialException
     */
    public function process(Withdraw $withdraw, OutgoingAccount $account): void
    {
        if ($withdraw->isProcessed()) {
            throw new \RuntimeException('Списание уже произведено');
        }

        if (!$withdraw->isConfirmed()) {
            throw new \RuntimeException('Операция не одобрена');
        }

        $availableBalance = $this->accountManager->getAvailableBalance($withdraw->getAccount());

        if ($availableBalance < $withdraw->getAmount()) {
            throw new InsufficientFundsException(
                $withdraw->getAccount(),
                $withdraw->getAmount(),
                'Недостаточно средств для выполнения операции вывода средств'
            );
        }

        $transactions = $this->transactionManager->create($withdraw->getAccount(), $account, $withdraw, false);

        $this->entityManager->transactional(function(EntityManagerInterface $em) use ($withdraw, $transactions) {

            $this->transactionManager->process($transactions);

            if ($withdraw->hasHold()) {
                $hold = $withdraw->getHold();
                $withdraw->setHold(null);
                $em->remove($hold);
            }

            $withdraw->setStatus(Withdraw::STATUS_DONE);

            $this->entityManager->flush();
        });
    }

    /**
     * @param Withdraw $withdraw
     */
    public function cancel(Withdraw $withdraw): void
    {
        //todo: необходимо реализовать смену статуса и снятие блокировка со счёта.
    }
}