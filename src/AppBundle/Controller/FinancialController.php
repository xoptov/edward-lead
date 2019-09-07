<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Account;
use AppBundle\Entity\Invoice;
use AppBundle\Event\InvoiceEvent;
use AppBundle\Event\WithdrawEvent;
use AppBundle\Service\AccountManager;
use AppBundle\Service\InvoiceManager;
use AppBundle\Service\WithdrawManager;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\MonetaryTransaction;
use AppBundle\Exception\FinancialException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\Type\DataTransformer\PhoneTransformer;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FinancialController extends Controller
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var AccountManager
     */
    private $accountManager;

    /**
     * @var InvoiceManager
     */
    private $invoiceManager;

    /**
     * @var WithdrawManager
     */
    private $withdrawManager;

    /**
     * @param EntityManagerInterface   $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param AccountManager           $accountManager
     * @param InvoiceManager           $invoiceManager
     * @param WithdrawManager          $withdrawManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        AccountManager $accountManager,
        InvoiceManager $invoiceManager,
        WithdrawManager $withdrawManager
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->accountManager = $accountManager;
        $this->invoiceManager = $invoiceManager;
        $this->withdrawManager = $withdrawManager;
    }

    /**
     * @Route("/financial/deposit", name="app_financial_deposit", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function depositAction(Request $request): Response
    {
        $formBuilder = $this->createFormBuilder()
            ->add('amount', MoneyType::class, [
                'divisor' => Account::DIVISOR,
                'currency' => null,
                'constraints' => [
                    new NotBlank(['message' => 'Необходимо указать сумму для пополнения']),
                    new GreaterThan(['value' => 0, 'message' => 'Сумма для пополнения должна быть больше ноля'])
                ]
            ])
            ->add('phone', TextType::class, ['required' => false])
            ->add('submit', SubmitType::class);

        $formBuilder->get('phone')->addViewTransformer(new PhoneTransformer());
        $form = $formBuilder->getForm();
        $user = $this->getUser();

        if ($request->isMethod(Request::METHOD_POST)) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();

                $invoice = $this->invoiceManager->create($user, $data['amount'], $data['phone'], false);
                $this->eventDispatcher->dispatch(InvoiceEvent::CREATED, new InvoiceEvent($invoice));

                $this->entityManager->flush();

                $this->addFlash('success', 'Запрос на пополнение баланса принят');

                return $this->redirectToRoute('app_financial_billing');
            }
        }

        return $this->render('@App/Financial/deposit.html.twig', [
            'form' => $form->createView(),
            'currentPhone' => '+' . $user->getPhone()
        ]);
    }

    /**
     * @Route("/financial/billing", name="app_financial_billing", methods={"GET"})
     *
     * @return Response
     */
    public function billingAction(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $availableBalance = $this->accountManager->getAvailableBalance($user->getAccount(), Account::DIVISOR);
        $holdBalance = $this->accountManager->getHoldAmount($user->getAccount(), Account::DIVISOR);

        return $this->render('@App/Financial/billing.html.twig', [
            'availableBalance' => $availableBalance,
            'holdBalance' => $holdBalance
        ]);
    }

    /**
     * @Route("/financial/withdraw", name="app_financial_withdraw", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function withdrawAction(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $freeBalance = $this->accountManager->getAvailableBalance($user->getAccount());

        $formBuilder = $this->createFormBuilder();
        $formBuilder
            ->add('amount', MoneyType::class, [
                'divisor' => Account::DIVISOR,
                'currency' => null,
                'constraints' => [
                    new NotBlank(['message' => 'Необходимо указать сумму для вывода']),
                    new LessThanOrEqual([
                        'value' => $freeBalance,
                        'message' => 'Недостаточно средств для вывода'
                    ])
                ]
            ])
            ->add('submit', SubmitType::class);

        $form = $formBuilder->getForm();

        if ($request->isMethod(Request::METHOD_POST)) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();

                try {
                    $withdraw = $this->withdrawManager->create($user, $data['amount'], false);
                } catch (FinancialException $e) {
                    $this->addFlash('error', $e->getMessage());

                    return $this->render('@App/Financial/withdraw.html.twig', [
                        'form' => $form->createView()
                    ]);
                }

                $this->eventDispatcher->dispatch(
                    WithdrawEvent::CREATED,
                    new WithdrawEvent($withdraw)
                );

                $this->entityManager->flush();

                $this->addFlash('success', 'Зявка на вывод отправлена');

                return $this->redirectToRoute('app_financial_billing');
            }
        }

        return $this->render('@App/Financial/withdraw.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/financial/invoices", name="app_financial_invoices", methods={"GET"})
     *
     * @return Response
     */
    public function invoicesAction(): Response
    {
        $user = $this->getUser();

        $invoices = $this->entityManager
            ->getRepository(Invoice::class)
            ->getAllByUser($user);

        return $this->render('@App/Financial/invoices.html.twig', [
            'invoices' => $invoices
        ]);
    }

    /**
     * @Route("/financial/history", name="app_financial_history", methods={"GET"})
     *
     * @return Response
     */
    public function historyAction(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $transactions = $this->getDoctrine()
            ->getRepository(MonetaryTransaction::class)
            ->findBy(['account' => $user->getAccount()], ["createdAt" => "DESC"]);

        return $this->render('@App/Financial/history.html.twig', [
            'transactions' => $transactions
        ]);
    }
}