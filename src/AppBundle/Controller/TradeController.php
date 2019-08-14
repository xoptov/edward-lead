<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Trade;
use AppBundle\Entity\Account;
use AppBundle\Event\TradeEvent;
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
     * @Route("/trade/accept/{id}", name="app_trade_accept", methods={"GET"})
     *
     * @param Trade                    $trade
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return Response
     */
    public function acceptAction(Trade $trade, EventDispatcherInterface $eventDispatcher): Response
    {
        $lead = $trade->getLead();

        if (!$this->isGranted(TradeVoter::ACCEPT, $trade)) {
            $this->addFlash('error', 'У Вас нет прав для подтверждения качества лида');

            return $this->redirectToRoute('app_lead_show', ['id' => $lead->getId()]);
        }

        try {

            $feesAccount = $this->getDoctrine()->getRepository(Account::class)
                ->getFeesAccount();

            $this->tradeManager->accept($trade, $feesAccount);
            $eventDispatcher->dispatch(TradeEvent::ACCEPT, new TradeEvent($trade));

            $this->addFlash('success', 'Покупка лида завершена');

        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_lead_show', ['id' => $lead->getId()]);
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
                $eventDispatcher->dispatch(TradeEvent::REJECT, new TradeEvent($trade));
                $this->addFlash('success', 'Покупка отклонена');
            } else {
                $this->tradeManager->arbitrage($trade);
                $eventDispatcher->dispatch(TradeEvent::ARBITRAGE, new TradeEvent($trade));
                $this->addFlash('success', 'Покупка заморожена и передена в арбитраж');
            }
        } catch(\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_lead_show', ['id' => $lead->getId()]);
    }
}