<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Trade;
use AppBundle\Entity\Account;
use AppBundle\Event\TradeEvent;
use AppBundle\Service\TradeManager;
use AppBundle\Security\Voter\TradeVoter;
use AppBundle\Exception\FinancialException;
use AppBundle\Exception\OperationException;
use Doctrine\ORM\UnexpectedResultException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TradeController extends Controller
{
    /**
     * @var TradeManager
     */
    private $tradeManager;

    /**
     * @param TradeManager $tradeManager
     */
    public function __construct(TradeManager $tradeManager)
    {
        $this->tradeManager = $tradeManager;
    }

    /**
     * @Route("/trade/accept/{id}", name="app_trade_accept", methods={"GET"})
     *
     * @param Trade                    $trade
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return Response
     */
    public function acceptAction(Trade $trade, EventDispatcherInterface $eventDispatcher): Response
    {
        if (!$this->isGranted(TradeVoter::ACCEPT, $trade)) {
            $this->addFlash('error', 'У Вас нет прав для подтверждения качества лида');

            return $this->redirectToRoute('app_lead_show', ['id' => $trade->getLeadId()]);
        }

        try {
            $feesAccount = $this->getDoctrine()->getRepository(Account::class)
                ->getFeesAccount();

            $this->tradeManager->accept($trade, $feesAccount);
            $eventDispatcher->dispatch(TradeEvent::ACCEPTED, new TradeEvent($trade));

            $this->addFlash('success', 'Статус у лида назначен как "целевой"');
        } catch (FinancialException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (OperationException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (UnexpectedResultException $e) {
            $this->addFlash('error', 'Система поломалась');
        }

        return $this->redirectToRoute('app_lead_show', ['id' => $trade->getLeadId()]);
    }

    /**
     * @Route("/trade/reject/{id}", name="app_trade_reject", methods={"GET"})
     *
     * @param Trade                    $trade
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return Response
     */
    public function rejectAction(Trade $trade, EventDispatcherInterface $eventDispatcher): Response
    {
        if (!$this->isGranted(TradeVoter::REJECT, $trade)) {
            $this->addFlash('error', 'У Вас нет прав для отказа от указанной сделки');

            return $this->redirectToRoute('app_lead_show', ['id' => $trade->getLeadId()]);
        }

        try {

            $lead = $trade->getLead();

            if ($lead->hasRoom() && !$lead->getRoom()->isPlatformWarranty()) {
                $this->tradeManager->reject($trade);
                $this->addFlash('success', 'Статус у лида назначен как "не целевой"');
                $eventDispatcher->dispatch(TradeEvent::REJECTED, new TradeEvent($trade));
            } else {
                $this->tradeManager->arbitrage($trade);
                $this->addFlash('success', 'Лид заморожен и переден в арбитраж. Ожидайте ответа поддержки');
                $eventDispatcher->dispatch(TradeEvent::PROCEEDING, new TradeEvent($trade));
            }
        } catch(OperationException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_lead_show', ['id' => $trade->getLeadId()]);
    }

    /**
     * @Route("/trade/ask-callback/{id}", name="app_trade_ask_callback", methods={"GET"})
     *
     * @param Trade                    $trade
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return Response
     */
    public function askCallbackAction(Trade $trade, EventDispatcherInterface $eventDispatcher): Response
    {
        if (!$this->isGranted(TradeVoter::ASK_CALLBACK, $trade)) {
            $this->addFlash('error', 'У Вас нет прав на указание того что лид просил перезвонить');

            return $this->redirectToRoute('app_lead_show', ['id' => $trade->getLeadId()]);
        }

        try {
            $this->tradeManager->askCallback($trade);
            $this->addFlash('success', 'Не забудьте перезвонить в течении 48 часов. Иначе лид будет засчитан как "целевой"');
            $eventDispatcher->dispatch(TradeEvent::ASK_CALLBACK, new TradeEvent($trade));
        } catch (OperationException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_lead_show', ['id' => $trade->getLeadId()]);
    }

    /**
     * @return Response
     *
     * @throws \Exception
     */
    public function showResultModal(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $trades = $this->getDoctrine()
            ->getRepository(Trade::class)
            ->getByBuyerAndWarrantyAndIncomplete($user);

        foreach ($trades as $trade) {
            if ($this->tradeManager->isCanShowResultModal($trade)) {
                return $this->render('@App/Trade/select_result_modal.html.twig', ['trade' => $trade]);
            }
        }

        return new Response();
    }
}