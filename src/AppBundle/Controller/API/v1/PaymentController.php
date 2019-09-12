<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Entity\IncomeAccount;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\User;
use AppBundle\Event\InvoiceEvent;
use AppBundle\Service\InvoiceManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/api/v1")
 */
class PaymentController extends Controller
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var InvoiceManager
     */
    private $invoiceManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var string
     */
    private $paymentGatewayToken;

    /**
     * @param EntityManagerInterface   $entityManager
     * @param InvoiceManager           $invoiceManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param string                   $paymentGatewayToken
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        InvoiceManager $invoiceManager,
        EventDispatcherInterface $eventDispatcher,
        string $paymentGatewayToken
    )
    {
        $this->entityManager = $entityManager;
        $this->invoiceManager = $invoiceManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->paymentGatewayToken = $paymentGatewayToken;
    }

    /**
     * @Route("/app/status", name="api_app_status", methods={"GET"}, defaults={"_format":"json"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function apiAppStatusAction(Request $request): JsonResponse
    {
        try {
            return new JsonResponse(['code' => 0, 'response' => 'ok', 'result' => null]);
        } catch (\Exception $ex) {
            // TODO: Какая то ошибка....
            return new JsonResponse(['code' => 500, 'response' => 'server-error', 'result' => null], 500);
        }
    }

    /**
     * @Route("/payment/getinvoice/{hash}", name="api_payment_getinvoice", methods={"GET"}, defaults={"_format":"json"})
     *
     * @param null|string $hash
     *
     * @todo: Потом лучше использовать ParamConverter для загрузки Invoice из БД.
     *
     * @return JsonResponse
     */
    public function getInvoiceAction(?string $hash): JsonResponse
    {
        try {
            $invoice = $this->entityManager
                ->getRepository(Invoice::class)
                ->getByHash($hash);

            if (count($invoice) == 0 || $invoice[0] == null)
                return new JsonResponse(['code' => 1, 'response' => 'not-found-invoice', 'result' => null]);

            return new JsonResponse(['code' => 0, 'response' => 'ok', 'result' => $invoice[0]->getPaymentInfoArray()]);
        } catch (\Exception $ex) {
            // TODO: Какая то ошибка....
            return new JsonResponse(['code' => 500, 'response' => 'server-error', 'result' => null], 500);
        }
    }

    /**
     * @Route("/payment/createinvoice/{id_user}/{sum}", name="api_payment_createinvoice", methods={"GET"}, defaults={"_format":"json"})
     *
     * @param Request  $request
     * @param null|int $id_user
     * @param null|int $sum
     *
     * @todo: потом лучше доставать id_user и sum из объекта Request.
     *
     * @return JsonResponse
     */
    public function createInvoiceAction(Request $request, ?int $id_user, ?int $sum): JsonResponse
    {
        if (!$request->query->has('paymentGatewayToken')
            || $request->query->get('paymentGatewayToken') !== $this->paymentGatewayToken
        ) {
            return new JsonResponse(
                ['code' => 403, 'response' => 'forbidden', 'result' => null],
                Response::HTTP_FORBIDDEN
            );
        }

        try {
            $user = $this->entityManager
                ->getRepository(User::class)
                ->findBy(['id' => $id_user]);

            if (count($user) == 0 || $user[0] == null)
                return new JsonResponse(['code' => 1, 'response' => 'not-found-user', 'result' => null]);

            $invoice = $this->invoiceManager->create($user[0], $sum, '', false);
            $this->eventDispatcher->dispatch(InvoiceEvent::CREATED, new InvoiceEvent($invoice));
            $this->entityManager->flush();

            return new JsonResponse(['code' => 0, 'response' => 'ok', 'result' => $invoice->getPaymentInfoArray()]);
        } catch (\Exception $ex) {
            // TODO: Какая то ошибка....
            return new JsonResponse(['code' => 500, 'response' => 'server-error', 'result' => null], 500);
        }
    }

    /**
     * @Route("/payment/successinvoice/{id_invoice}/{description_name_account}", name="api_payment_successinvoice_fixed", methods={"GET"}, defaults={"_format":"json"})
     *
     * @param Request     $request
     * @param null|int    $id_invoice
     * @param null|string $description_name_account
     *
     * @todo: потом лучше доставать id_user и description_name_account из объекта Request.
     *
     * @return JsonResponse
     */
    public function successInvoiceFixedAction(Request $request, ?int $id_invoice, ?string $description_name_account): JsonResponse
    {
        if (!$request->query->has('paymentGatewayToken')
            || $request->query->get('paymentGatewayToken') !== $this->paymentGatewayToken
        ) {
            return new JsonResponse(
                ['code' => 403, 'response' => 'forbidden', 'result' => null],
                Response::HTTP_FORBIDDEN
            );
        }

        try {
            $invoice = $this->entityManager
                ->getRepository(Invoice::class)
                ->getById($id_invoice);

            if (count($invoice) == 0 || $invoice[0] == null)
                return new JsonResponse(['code' => 1, 'response' => 'not-found-invoice', 'result' => null]);

            if ($invoice[0]->isProcessed())
                return new JsonResponse(['code' => 2, 'response' => 'invoice-has-processed', 'result' => null]);

            $incomeAccount = $this->entityManager
                ->getRepository(IncomeAccount::class)
                ->findBy(['description' => $description_name_account]);

            if (count($incomeAccount) == 0 || $incomeAccount[0] == null)
                return new JsonResponse(['code' => 3, 'response' => 'not-found-account', 'result' => null]);

            $this->invoiceManager->process($invoice[0], $incomeAccount[0]);

            return new JsonResponse(['code' => 0, 'response' => 'ok', 'result' => $invoice[0]->getPaymentInfoArray()]);
        } catch (\Exception $ex) {
            // TODO: Какая то ошибка....
            return new JsonResponse(['code' => 500, 'response' => 'server-error', 'result' => null], 500);
        }
    }

    /**
     * @Route("/payment/getcompanyfromuser/{id_user}", name="api_payment_successinvoice", methods={"GET"}, defaults={"_format":"json"})
     *
     * @param null|int $id_user
     *
     * @todo: Потом лучше использовать ParamConverter для загрузки юзера.
     *
     * @return JsonResponse
     */
    public function getCompanyFromUser(?int $id_user): JsonResponse
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findBy(['id' => $id_user]);

        if (count($user) == 0 || $user[0] == null)
            return new JsonResponse(['code' => 1, 'response' => 'not-found-user', 'result' => null]);

        $company = $user[0]->getCompany();

        if ($company == null)
            return new JsonResponse(['code' => 2, 'response' => 'not-found-company', 'result' => null]);

        $result = [
            'id' => $company->getId(),
            'name' => $company->getLargeName(),
            'inn' => $company->getInn(),
            'kpp' => $company->getKpp(),
            'address' => $company->getAddress()
        ];

        return new JsonResponse(['code' => 0, 'response' => 'ok', 'result' => $result]);
    }
}