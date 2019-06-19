<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Lead;
use Psr\Log\LoggerInterface;
use AppBundle\Entity\Account;
use AppBundle\Entity\PhoneCall;
use AppBundle\Form\Type\CallbackType;
use AppBundle\Service\PhoneCallManager;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Exception\OperationException;
use AppBundle\Exception\PhoneCallException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Exception\InsufficientFundsException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
    public function __construct(EntityManagerInterface $entityManager, PhoneCallManager $phoneCallManager)
    {
        $this->entityManager = $entityManager;
        $this->phoneCallManager = $phoneCallManager;
    }

    /**
     * @Route("/telephony/make-call/{lead}", name="app_telephony_make_call", methods={"GET"})
     *
     * @param Lead $lead
     *
     * @return Response
     */
    public function makeCallAction(Lead $lead): Response
    {
        if (!$this->isGranted('FIRST_CALL', $lead)) {
            return new JsonResponse(
                ['message' => 'Для звонка лиду вы должны его сначала зарезервировать'],
                Response::HTTP_FORBIDDEN
            );
        }

        try {
            $phoneCall = $this->phoneCallManager->create($this->getUser(), $lead, false);
            $this->phoneCallManager->makeCall($phoneCall);
        } catch (PhoneCallException $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (OperationException $e) {
            /** @var PhoneCall $phoneCall */
            $phoneCall = $e->getOperation();
            $hold = $phoneCall->getHold();

            $phoneCall
                ->setStatus(PhoneCall::STATUS_ERROR)
                ->setDescription($e->getMessage())
                ->setHold(null);

            $this->entityManager->remove($hold);
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
     * @Route("/telephony/callback", name="app_telephony_callback", methods={"POST"})
     *
     * @param Request         $request
     * @param LoggerInterface $logger
     *
     * @return Response
     * @todo: переделать обработку результата звонка после ответа серверу. KERNEL_TERMINATE событие.
     */
    public function callbackAction(Request $request, LoggerInterface $logger): Response
    {
//        $logger->debug('Callback from PBX', $request->request->all());
//        return new Response('Request received!');

        $form = $this->createForm(CallbackType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            if (!isset($data['externalId'])) {
                return new JsonResponse(['message' => 'Не указан call_id']);
            }

            $phoneCall = $this->entityManager->getRepository(PhoneCall::class)
                ->findOneBy(['externalId' => $data['externalId']]);

            if (!$phoneCall) {
                return new JsonResponse(['message' => 'Вызов с указаным call_id не найден']);
            }

            $this->phoneCallManager->process($phoneCall, $data);

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