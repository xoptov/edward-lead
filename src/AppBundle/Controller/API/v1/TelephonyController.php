<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Entity\Trade;
use AppBundle\Entity\User;
use Psr\Log\LoggerInterface;
use AppBundle\Entity\Account;
use AppBundle\Entity\PhoneCall;
use AppBundle\Entity\PBX\Callback;
use AppBundle\Service\PhoneCallManager;
use AppBundle\Security\Voter\TradeVoter;
use AppBundle\Form\Type\PBXCallbackType;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Service\PBXCallbackManager;
use AppBundle\Exception\PhoneCallException;
use AppBundle\Exception\OperationException;
use AppBundle\Exception\RequestCallException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Exception\InsufficientFundsException;

/**
 * @Route("/api/v1")
 */
class TelephonyController extends APIController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var PBXCallbackManager
     */
    private $pbxCallbackManager;

    /**
     * @var PhoneCallManager
     */
    private $phoneCallManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @param PBXCallbackManager     $pbxCallbackManager
     * @param PhoneCallManager       $phoneCallManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        PBXCallbackManager $pbxCallbackManager,
        PhoneCallManager $phoneCallManager
    ) {
        $this->entityManager = $entityManager;
        $this->pbxCallbackManager = $pbxCallbackManager;
        $this->phoneCallManager = $phoneCallManager;
    }


    /**
     * @Route("/telephony/call/{trade}", name="api_v1_telephony_make_call", methods={"GET"}, defaults={"_format": "json"})
     *
     * @param LoggerInterface $logger
     * @param Trade           $trade
     *
     * @return JsonResponse
     */
    public function getCallAction(
        LoggerInterface $logger,
        Trade $trade
    ): JsonResponse {

        if (!$this->isGranted(TradeVoter::MAKE_CALL, $trade)) {
            return new JsonResponse(
                ['error' => 'Для звонка лиду вы должны его сначала купить'],
                Response::HTTP_FORBIDDEN
            );
        }

        $telephonyEnabled = $this->getParameter('telephony_enabled');

        if (!$telephonyEnabled) {
            return new JsonResponse(['error' => 'Телефония отключена'], Response::HTTP_BAD_REQUEST);
        }

        /** @var User $user */
        $user = $this->getUser();

        try {
            $phoneCall = $this->phoneCallManager->create($user, $trade);
            $this->phoneCallManager->requestConnection($phoneCall);
        } catch (RequestCallException $e) {
            $errorMessage = 'Ошибка при запросе на соединение по телефону';
            $logger->error($errorMessage, ['message' => $e->getMessage()]);

            return new JsonResponse(['error' => $errorMessage], Response::HTTP_BAD_REQUEST);
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

            $logger->error($e->getMessage());

            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (InsufficientFundsException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage(), 'amount' => $e->getNeedle() / Account::DIVISOR],
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
        if ('dev' === $this->getParameter('kernel.environment')) {
            $logger->debug('Callback from PBX', ['data' => $request->request->all()]);
        }

        $options = [
            'fields_map' => [
                '[call_id]'         => '[phoneCall]',
                '[event]'           => '[event]',
                '[recording]'       => '[audioRecord]',
                '[call1_phone]'     => '[firstShoulder][phone]',
                '[call1_billsec]'   => '[firstShoulder][billSec]',
                '[call1_tarif]'     => '[firstShoulder][tariff]',
                '[call1_start_at]'  => '[firstShoulder][startAt]',
                '[call1_answer_at]' => '[firstShoulder][answerAt]',
                '[call1_hangup_at]' => '[firstShoulder][hangupAt]',
                '[call1_status]'    => '[firstShoulder][status]',
                '[call2_phone]'     => '[secondShoulder][phone]',
                '[call2_billsec]'   => '[secondShoulder][billSec]',
                '[call2_tarif]'     => '[secondShoulder][tariff]',
                '[call2_start_at]'  => '[secondShoulder][startAt]',
                '[call2_answer_at]' => '[secondShoulder][answerAt]',
                '[call2_hangup_at]' => '[secondShoulder][hangupAt]',
                '[call2_status]'    => '[secondShoulder][status]'
            ]
        ];

        $form = $this->createForm(PBXCallbackType::class, null, $options);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->responseErrors($form->getErrors(true));
        }

        try {
            /** @var Callback $pbxCallback */
            $pbxCallback = $form->getData();
            $this->entityManager->persist($pbxCallback);

            $this->pbxCallbackManager->process($pbxCallback);
            $this->phoneCallManager->process($pbxCallback->getPhoneCall(), $pbxCallback);

        } catch (PhoneCallException $e) {
            $this->entityManager->flush();
            $errorMessage = 'Ошибка обработки телефонного вызова';
            $logger->error($errorMessage, ['message' => $e->getMessage()]);

            return new JsonResponse(['error' => $errorMessage], Response::HTTP_BAD_REQUEST);

        } catch (\Exception $e) {
            $errorMessage = 'Ошибка обработки callback от PBX';
            $logger->error($errorMessage, ['message' => $e->getMessage()]);

            return new JsonResponse(['error' => $errorMessage], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Данные о звонке успешно приняты']);
    }
}