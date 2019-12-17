<?php

namespace AppBundle\Service;

use AppBundle\Entity\ClientAccount;
use AppBundle\Entity\OutgoingAccount;
use AppBundle\Entity\User;
use AppBundle\Entity\Withdraw;
use AppBundle\Entity\WithdrawConfirm;
use AppBundle\Exception\FinancialException;
use AppBundle\Exception\InsufficientFundsException;
use Doctrine\ORM\EntityManagerInterface;

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
     * @var int
     */
    private $minimalAmount;

    /**
     * @param EntityManagerInterface $entityManager
     * @param AccountManager         $accountManager
     * @param HoldManager            $holdManager
     * @param TransactionManager     $transactionManager
     * @param int                    $minimalAmount
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        AccountManager $accountManager,
        HoldManager $holdManager,
        TransactionManager $transactionManager,
        int $minimalAmount
    ) {
        $this->entityManager = $entityManager;
        $this->accountManager = $accountManager;
        $this->holdManager = $holdManager;
        $this->transactionManager = $transactionManager;
        $this->minimalAmount = $minimalAmount;
    }

    /**
     * @param User $user
     * @param int  $amount
     * @param bool $flush
     *
     * @return Withdraw
     *
     * @throws FinancialException
     */
    public function create(
        User $user,
        int $amount,
        bool $flush = true
    ): Withdraw
    {

        // TODO uncomment
//        if ($amount < $this->minimalAmount) {
//            throw new FinancialException(
//                'Минимальная сумма вывода составляет ' . (int)ceil($this->minimalAmount / Account::DIVISOR) . ' руб'
//            );
//        }

        $balance = $this->accountManager->getAvailableBalance($user->getAccount());

        // TODO uncomment
//        if ($balance < $amount) {
//            throw new FinancialException('Недостаточно средств для вывода');
//        }

        $withdraw = new Withdraw();
        $withdraw
            ->setUser($user)
            ->setDescription('Вывод средств с баланса')
            ->setAmount($amount);

        $this->entityManager->persist($withdraw);

        $this->addHold($user->getAccount(), $withdraw, $amount);

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


        if ($withdraw->hasHold()) {
            $hold = $withdraw->getHold();
            if ($hold->getAmount() < $withdraw->getAmount()) {
                throw new InsufficientFundsException(
                    $withdraw->getAccount(),
                    $withdraw->getAmount(),
                    'Замороженно недостаточно средств для выполнения операции'
                );
            }
        } elseif ($this->accountManager->getAvailableBalance($withdraw->getAccount()) < $withdraw->getAmount()) {
            throw new InsufficientFundsException(
                $withdraw->getAccount(),
                $withdraw->getAmount(),
                'Недостаточно средств для выполнения операции вывода средств'
            );
        }

        $transactions = $this->transactionManager->create($withdraw->getAccount(), $account, $withdraw, false);

        $this->entityManager->transactional(function(EntityManagerInterface $entityManager) use ($withdraw, $transactions) {

            $this->transactionManager->process($transactions);

            $this->removeHold($withdraw);

            $withdraw->setStatus(Withdraw::STATUS_DONE);

            $entityManager->flush();
        });
    }

    /**
     * @param Withdraw $withdraw
     */
    public function cancel(Withdraw $withdraw): void
    {
        $withdraw->setStatus(Withdraw::STATUS_CANCELED);
        $this->removeHold($withdraw);
    }

    /**
     * @param Withdraw $withdraw
     * @param User     $user
     */
    public function confirm(Withdraw $withdraw, User $user): void
    {
        $confirm = new WithdrawConfirm();
        $confirm->setAuthor($user);
        $confirm->setWithdraw($withdraw);

        $this->entityManager->persist($confirm);

        $withdraw->setConfirm($confirm);
    }

    /**
     * @param Withdraw $withdraw
     */
    private function removeHold(Withdraw $withdraw): void
    {
        if ($withdraw->hasHold()) {
            $hold = $withdraw->getHold();
            $withdraw->setHold(null);
            $this->entityManager->remove($hold);
        }
    }

    /**
     * @param ClientAccount $account
     * @param Withdraw      $withdraw
     * @param int           $amount
     */
    private function addHold(
        ClientAccount $account,
        Withdraw $withdraw,
        int $amount
    ): void {

        $hold = $this->holdManager
            ->create($account, $withdraw, $amount, false);

        $withdraw->setHold($hold);
    }
}