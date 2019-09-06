<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\IncomeAccount;
use AppBundle\Exception\AccountException;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Exception\OperationException;

class InvoiceManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TransactionManager
     */
    private $transactionManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @param TransactionManager     $transactionManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TransactionManager $transactionManager
    ) {
        $this->entityManager = $entityManager;
        $this->transactionManager = $transactionManager;
    }

    /**
     * @param User        $user
     * @param int         $amount
     * @param string|null $phone
     * @param bool|null   $flush
     *
     * @return Invoice
     */
    public function create(User $user, int $amount, ?string $phone = null, ?bool $flush = true): Invoice
    {
        $invoice = new Invoice();
        $invoice
            ->setHash(md5(microtime()))
            ->setUser($user)
            ->setPhone($phone)
            ->setDescription('Пополнение баланса')
            ->setAmount($amount);

        $this->entityManager->persist($invoice);

        if ($flush) {
            $this->entityManager->flush();
        }

        return $invoice;
    }

    /**
     * @param Invoice       $invoice
     * @param IncomeAccount $account
     *
     * @throws OperationException
     * @throws AccountException
     */
    public function process(Invoice $invoice, IncomeAccount $account): void
    {
        if ($invoice->isProcessed()) {
            throw new OperationException($invoice, 'Инвойс уже обработан');
        }

        $transactions = $this->transactionManager->create($account, $invoice->getAccount(), $invoice);

        $this->entityManager->transactional(function(EntityManagerInterface $em) use ($invoice, $transactions){

            foreach ($transactions as $transaction) {
                $em->persist($transaction);
            }

            $this->transactionManager->process($transactions);
            $invoice->setStatus(Invoice::STATUS_DONE);

            $this->entityManager->flush();
        });
    }

    /**
     * @param Invoice $invoice
     */
    public function cancel(Invoice $invoice): void
    {
        $invoice->setStatus(Invoice::STATUS_CANCELED);
    }
}