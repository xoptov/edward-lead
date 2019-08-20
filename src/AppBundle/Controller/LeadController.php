<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Lead;
use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use AppBundle\Entity\Trade;
use AppBundle\Entity\PhoneCall;
use AppBundle\Service\TradeManager;
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
     * @return Response
     */
    public function myAction(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $data = null;

        if ($this->isGranted('ROLE_WEBMASTER')) {
            $leads = $this->getDoctrine()
                ->getRepository(Lead::class)
                ->findBy(["user" => $user], ["id" => "DESC"]);
            $data = array(
                'leads' => $leads
            );
        }

        if ($this->isGranted('ROLE_COMPANY')) {
            $trades = $this->getDoctrine()
                ->getRepository(Trade::class)
                ->findBy(["buyer" => $user], ["id" => "DESC"]);

            $phoneCalls = $this->getDoctrine()
                ->getRepository(PhoneCall::class)
                ->getCallsWithTrades($user, $trades);

            $data = array(
                'trades'    => $trades,
                'calls'     => $phoneCalls
            );
        }

        return $this->render('@App/Lead/my.html.twig', $data);
    }

    /**
     * @Route("/lead/{id}", name="app_lead_show", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param Lead $lead
     *
     * @return Response
     */
    public function showLeadAction(Lead $lead): Response
    {
        if (!$this->isGranted(LeadViewVoter::OPERATION, $lead)) {
            $this->addFlash('error', 'У Вас нет прав на просмотр лида');

            return $this->redirectToRoute('app_leads_my');
        }

        if ($lead->getUser() === $this->getUser()) {
            return $this->render('@App/Lead/show_before_buy.html.twig', ['lead' => $lead]);
        } elseif ($lead->getBuyer() === $this->getUser()) {
            return $this->render('@App/Lead/show_after_buy.html.twig', ['lead' => $lead]);
        } else {
            return $this->render("@App/Lead/show_before_buy.html.twig", ["lead" => $lead]);
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
            $trade = $this->tradeManager->start($this->getUser(), $lead->getUser(), $lead, $lead->getPrice());
            $this->addFlash('success', "Резервирование лида выполнено, номер резервирования {$trade->getId()}.");
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_leads_my');
    }



    /**
     * @return Response
     *
     * @throws \Exception
     */
    public function unsetReserveModal(): Response
    {
        $user = $this->getUser();

        $lead = $this->getDoctrine()->getRepository('AppBundle:Lead')
            ->getByUserAndReserved($user);

        if ($lead) {
            $room = $lead->getRoom();

            if ($room && !$room->isPlatformWarranty()) {
                return new Response();
            }

            $phoneCall = $this->getDoctrine()->getRepository(PhoneCall::class)
                ->getAnsweredPhoneCallByLeadAndCaller($lead, $user);

            if ($phoneCall) {
                return $this->render('@App/Lead/reserved.html.twig', ['lead' => $lead]);
            }
        }

        return new Response();
    }
}
