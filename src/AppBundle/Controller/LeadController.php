<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Lead;
use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use AppBundle\Entity\Trade;
use AppBundle\Entity\PhoneCall;
use AppBundle\Service\FeesManager;
use AppBundle\Service\TimerManager;
use AppBundle\Service\TradeManager;
use AppBundle\Service\PhoneCallManager;
use AppBundle\Security\Voter\LeadBuyVoter;
use AppBundle\Security\Voter\LeadViewVoter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LeadController extends Controller
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
     * @Route("/lead/create/form/{room}", name="app_lead_create_form", methods={"GET"}, defaults={"room": null})
     *
     * @param Room|null $room
     *
     * @return Response
     */
    public function createFormAction(?Room $room = null): Response
    {
        return $this->render('@App/Lead/form.html.twig', [
            'room' => $room
        ]);
    }

    /**
     * @Route("/lead/edit/form/{lead}", name="app_lead_edit_form", methods={"GET"})
     *
     * @param Lead $lead
     *
     * @return Response
     */
    public function editFormAction(Lead $lead): Response
    {
        return $this->render('@App/Lead/form.html.twig', [
            'lead' => $lead
        ]);
    }

    /**
     * @Route("/leads/my", name="app_leads_my", methods={"GET"})
     *
     * @param TimerManager $timerManager
     *
     * @return Response
     */
    public function myAction(
        TimerManager $timerManager
    ): Response {

        /** @var User $user */
        $user = $this->getUser();
        $data = null;

        if ($this->isGranted('ROLE_WEBMASTER')) {

            $leads = $this->getDoctrine()
                ->getRepository(Lead::class)
                ->findBy(["user" => $user], ['id' => 'DESC']);

            $data = array(
                'leads' => $leads
            );
        } elseif ($this->isGranted('ROLE_COMPANY')) {

            $trades = $this->getDoctrine()
                ->getRepository(Trade::class)
                ->findBy(['buyer' => $user], ['id' => 'DESC']);

            $phoneCalls = $this->getDoctrine()
                ->getRepository(PhoneCall::class)
                ->getCallsWithTrades($user, $trades);

            $data = array(
                'trades'    => $trades,
                'calls'     => $phoneCalls
            );
        }

        $now = $timerManager->createDateTime();

        return $this->render('@App/Lead/my.html.twig', array_merge($data, ['now' => $now]));
    }

    /**
     * @Route("/lead/{id}", name="app_lead_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param Lead             $lead
     * @param TradeManager     $tradeManager
     * @param FeesManager      $feesManager
     * @param PhoneCallManager $phoneCallManager
     * @param TimerManager     $timerManager
     *
     * @return Response
     */
    public function showLeadAction(
        Lead $lead,
        TradeManager $tradeManager,
        FeesManager $feesManager,
        PhoneCallManager $phoneCallManager,
        TimerManager $timerManager
    ): Response {
        if (!$this->isGranted(LeadViewVoter::OPERATION, $lead)) {
            $this->addFlash('error', 'У Вас нет прав на просмотр лида');

            return $this->redirectToRoute('app_leads_my');
        }

        /** @var User $user */
        $user = $this->getUser();

        if ($lead->isOwner($user)) {
            $now = $timerManager->createDateTime();

            return $this->render('@App/Lead/show_before_buy.html.twig', [
                'lead' => $lead,
                'priceWithFee' => $tradeManager->calculateCostWithFee($lead),
                'fee' => $feesManager->getCommissionForBuyingLead($lead)
            ]);
        } elseif ($lead->isBuyer($user)) {
            $canMakeCall = $phoneCallManager->isCanMakeCall($user, $lead);

            return $this->render('@App/Lead/show_after_buy.html.twig', [
                'lead' => $lead,
                'canMakeCall' => $canMakeCall
            ]);

        } else {
            return $this->render("@App/Lead/show_before_buy.html.twig", [
                'lead' => $lead,
                'priceWithFee' => $tradeManager->calculateCostWithMarginWithFee($lead),
                'fee' => $feesManager->getCommissionForBuyingLead($lead)
            ]);
        }
    }

    /**
     * @Route("/lead/buy/{id}", name="app_lead_buy", methods={"GET"})
     *
     * @param Lead $lead
     *
     * @return Response
     */
    public function buyLeadAction(Lead $lead): Response
    {
        if (!$this->isGranted(LeadBuyVoter::OPERATION, $lead)) {
            $this->addFlash('error', 'У вас нет прав на покупку этого лида');

            return $this->redirectToRoute('app_leads_my');
        }

        try {
            /** @var User $buyer */
            $buyer = $this->getUser();
            $seller = $lead->getUser();

            $trade = $this->tradeManager->start($buyer, $seller, $lead);
            $this->addFlash('success', "Резервирование лида выполнено, номер резервирования {$trade->getId()}.");
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_lead_show', ['id' => $lead->getId()]);
    }
}
