<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Trade;
use AppBundle\Entity\Account;
use AppBundle\Event\LeadEvent;
use AppBundle\Service\TradeManager;
use AppBundle\Security\Voter\TradeVoter;
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
     * @Route("/trade/success/{id}", name="app_trade_success", methods={"GET"})
     *
     * @param Trade                    $trade
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return Response
     */
    public function successBuyAction(Trade $trade, EventDispatcherInterface $eventDispatcher): Response
    {
        $lead = $trade->getLead();

        if (!$this->isGranted(TradeVoter::SUCCESS, $trade)) {
            $this->addFlash('error', 'У Вас нет прав для подтверждения качества лида');

            return $this->redirectToRoute('app_lead_show', ['id' => $lead->getId()]);
        }

        try {
            $feesAccount = $this->getDoctrine()->getRepository(Account::class)
                ->getFeesAccount();

            $this->tradeManager->finishSuccess($trade, $feesAccount);
            $eventDispatcher->dispatch(LeadEvent::SOLD, new LeadEvent($trade->getLead()));

            $this->addFlash('success', 'Покупка лида завершена');

        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_lead_show', ['id' => $lead->getId()]);
    }

    /**
     * @Route("/trade/reject/{id}/{status}", name="app_trade_reject", methods={"GET"})
     *
     * @param Trade                    $trade
     * @param string                   $status
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return Response
     */
    public function rejectBuyAction(Trade $trade, string $status, EventDispatcherInterface $eventDispatcher): Response
    {
        $lead = $trade->getLead();

        if (!$this->isGranted(TradeVoter::REJECT, $trade)) {
            $this->addFlash('error', 'У Вас нет прав для отказа от указанной сделки');

            return $this->redirectToRoute('app_lead_show', ['id' => $lead->getId()]);
        }

        try {
            $this->tradeManager->finishRejectByLeadStatus($trade, $status);
            $eventDispatcher->dispatch(LeadEvent::NO_TARGET, new LeadEvent($lead));

            $this->addFlash('success', 'Покупка заморожена и передена в арбитраж');

        } catch(\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_lead_show', ['id' => $lead->getId()]);
    }
}