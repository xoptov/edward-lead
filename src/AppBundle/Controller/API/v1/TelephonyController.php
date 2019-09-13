<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Entity\Lead;
use AppBundle\Entity\Trade;
use AppBundle\Security\Voter\TradeVoter;
use Psr\Log\LoggerInterface;
use AppBundle\Entity\Account;
use AppBundle\Entity\PhoneCall;
use AppBundle\Service\PhoneCallManager;
use AppBundle\Form\Type\PBXCallbackType;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Exception\OperationException;
use AppBundle\Exception\PhoneCallException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Security\Voter\LeadFirstCallVoter;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Exception\InsufficientFundsException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/api/v1")
 */
class TelephonyController extends Controller
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var PhoneCallManager
     */
    private $phoneCallManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @param PhoneCallManager       $phoneCallManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        PhoneCallManager $phoneCallManager
    ) {
        $this->entityManager = $entityManager;
        $this->phoneCallManager = $phoneCallManager;
    }


    /**
     * @Route("/telephony/call/{trade}", name="api_v1_telephony_make_call", methods={"GET"}, defaults={"_format": "json"})
     *
     * @param Trade $trade
     *
     * @return JsonResponse
     */
    public function getCallAction(Trade $trade): JsonResponse
    {
        if (!$this->isGranted(TradeVoter::MAKE_CALL, $trade)) {
            return new JsonResponse(
                ['message' => 'Для звонка лиду вы должны его сначала купить'],
                Response::HTTP_FORBIDDEN
            );
        }

        try {
            $phoneCall = $this->phoneCallManager->create($this->getUser(), $trade, false);
            $this->phoneCallManager->requestConnection($phoneCall);
        } catch (PhoneCallException $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (OperationException $e) {
            /** @var PhoneCall $phoneCall */
            $phoneCall = $e->getOperation();

            $phoneCall
                ->setStatus(PhoneCall::STATUS_ERROR)
                ->setDescription($e->getMessage());

            if ($phoneCall->hasHold()) {
                $hold = $phoneCall->takeHold();
                $this->entityManager->remove($hold);
            }

            $this->entityManager->flush();

            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (InsufficientFundsException $e) {
            return new JsonResponse(
                ['message' => $e->getMessage(), 'amount' => $e->getNeedle() / Account::DIVISOR],
                Response::HTTP_PAYMENT_REQUIRED
            );
        }

        return new JsonResponse(['message' => 'Запрос на соединение принят, ожидайте соединение с лидом']);
    }

    /**
     * @Route("/telephony/callback", name="api_v1_telephony_callback", methods={"POST"}, defaults={"_format": "json"})
     *
     * @param Request         $request
     * @param LoggerInterface $logger
     *
     * @return JsonResponse
     * @todo: переделать обработку результата звонка после ответа серверу. KERNEL_TERMINATE событие.
     */
    public function postCallbackAction(Request $request, LoggerInterface $logger): JsonResponse
    {
//        if ('dev' === $this->getParameter('kernel.environment')) {
//            $logger->debug('Callback from PBX', ['data' => $request->request->all()]);
//            return new JsonResponse(['message' => 'Callback received']);
//        }

        $form = $this->createForm(PBXCallbackType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $pbxCallback = $form->getData();
                $this->entityManager->persist($pbxCallback);
                $this->phoneCallManager->process($pbxCallback);
            } catch (\Exception $e) {
                $logger->error('Ошибка обработки callback от PBX', ['message' => $e->getMessage()]);

                return new JsonResponse(['message' => 'Ошибка обработки callback запроса'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return new JsonResponse(['message' => 'Данные о звонке успешно приняты']);
        }

        $errors = [];

        foreach ($form->getErrors(true) as $error) {
            $errors[] = [
                'field' => $error->getOrigin()->getName(),
                'message' => $error->getMessage()
            ];
        }

        $logger->error('Error callback from PBX', $errors);

        return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
    }
}