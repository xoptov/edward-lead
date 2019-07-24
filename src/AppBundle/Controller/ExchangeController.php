<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Lead;
use AppBundle\Entity\User;
use AppBundle\Entity\Trade;
use AppBundle\Entity\Account;
use AppBundle\Event\LeadEvent;
use AppBundle\Entity\PhoneCall;
use AppBundle\Service\LeadManager;
use AppBundle\Service\TradeManager;
use AppBundle\Security\Voter\TradeVoter;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Security\Voter\LeadBuyVoter;
use AppBundle\Security\Voter\LeadViewVoter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ExchangeController extends Controller
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var LeadManager
     */
    private $leadManager;

    /**
     * @var TradeManager
     */
    private $tradeManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @param LeadManager            $leadManager
     * @param TradeManager           $tradeManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        LeadManager $leadManager,
        TradeManager $tradeManager
    ) {
        $this->entityManager = $entityManager;
        $this->leadManager = $leadManager;
        $this->tradeManager = $tradeManager;
    }

    /**
     * @Route("/exchange", name="app_exchange", methods={"GET"})
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('@App/Exchange/index.html.twig');
    }

    /**
     * @Route("/exchange/my-leads", name="app_exchange_my_leads", methods={"GET"})
     *
     * @return Response
     */
    public function myLeadsAction(): Response
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

        return $this->render('@App/Exchange/my_leads.html.twig', $data);
    }

    /**
     * @Route("/exchange/leads", name="app_exchange_leads", methods={"GET"}, defaults={"_format"="json"})
     *
     * @return JsonResponse
     */
    public function getOffersAction(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $company = $user->getCompany();

        if ($company) {
            $cities = $company->getCities();

            if ($cities->isEmpty()) {
                return new JsonResponse();
            }

            $leads = $this->entityManager->getRepository(Lead::class)
                ->getByActiveAndCities($cities->toArray());
        } else {
            $leads = $this->entityManager->getRepository(Lead::class)
                ->getByActive();
        }

        $result = [];

        foreach ($leads as $lead) {
            $result[] = [
                'id' => $lead->getId(),
                'created_at' => $lead->getCreatedAtTimestamp(),
                'stars' => $this->leadManager->estimateStars($lead),
                'city' => $lead->getCityName(),
                'cpa' => false,
                'audio_record' => $lead->hasAudioRecord(),
                'channel' => $lead->getChannelName(),
                'price' => $lead->getPrice(Account::DIVISOR)
            ];
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/exchange/trades", name="app_exchange_trades", methods={"GET"}, defaults={"_format"="json"})
     *
     * @return JsonResponse
     */
    public function getTradesAction(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $company = $user->getCompany();

        if ($company) {
            $cities = $company->getCities();

            if ($cities->isEmpty()) {
                return new JsonResponse();
            }

            $trades = $this->entityManager->getRepository(Trade::class)
                ->getByCitiesAndStatus($cities->toArray(), Trade::STATUS_ACCEPTED);
        } else {
            $trades = $this->entityManager->getRepository(Trade::class)
                ->findBy(['status' => Trade::STATUS_ACCEPTED]);
        }

        $result = [];

        /** @var Trade $trade */
        foreach ($trades as $trade) {
            $lead = $trade->getLead();
            $result[] = [
                'id' => $trade->getId(),
                'created_at' => $trade->getCreatedAtTimestamp(),
                'lead' => $lead->getId(),
                'buyer' => $trade->getBuyerId(),
                'seller' => $trade->getSellerId(),
                'stars' => $this->leadManager->estimateStars($lead),
                'audio_record' => $lead->hasAudioRecord(),
                'city' => $lead->getCityName(),
                'cpa' => false,
                'price' => $trade->getAmount(Account::DIVISOR)
            ];
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/exchange/lead/{id}", name="app_exchange_show_lead", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @param Lead $lead
     *
     * @return Response
     */
    public function showLeadAction(Lead $lead)
    {
        if (!$this->isGranted(LeadViewVoter::OPERATION, $lead)) {
            $this->addFlash('error', 'У Вас нет прав на просмотр лида');

            return $this->redirectToRoute('app_exchange_my_leads');
        }

        if ($lead->getUser() === $this->getUser()) {
            return $this->render('@App/Exchange/show_lead_before.html.twig', ['lead' => $lead]);
        } elseif ($lead->getBuyer() === $this->getUser()) {
            return $this->render('@App/Exchange/show_lead_after.html.twig', ['lead' => $lead]);
        } else {
            return $this->render("@App/Exchange/show_lead_before.html.twig", ["lead" => $lead]);
        }
    }

    /**
     * @Route("/exchange/lead/buy/{id}", name="app_exchange_buy_lead", methods={"GET"})
     *
     * @param Lead         $lead
     *
     * @return Response
     */
    public function buyLeadAction(Lead $lead): Response
    {
        if (!$this->isGranted(LeadBuyVoter::OPERATION, $lead)) {
            $this->addFlash('error', 'У вас нет прав на покупку этого лида');

            return $this->redirectToRoute('app_exchange');
        }

        try {
            $trade = $this->tradeManager->start($this->getUser(), $lead->getUser(), $lead, $lead->getPrice());
            $this->addFlash('success', "Резервирование лида выполнено, номер резервирования {$trade->getId()}.");
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_exchange');
    }

    /**
     * @Route("/exchange/trade/success/{id}", name="app_exchange_trade_success", methods={"GET"})
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

            return $this->redirectToRoute('app_exchange_show_lead', ['id' => $lead->getId()]);
        }

        try {

            $feesAccount = $this->entityManager->getRepository(Account::class)
                ->getFeesAccount();

            $this->tradeManager->finishSuccess($trade, $feesAccount);
            $eventDispatcher->dispatch(LeadEvent::SOLD, new LeadEvent($trade->getLead()));

            $this->addFlash('success', 'Покупка лида завершена');

        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_exchange_show_lead', ['id' => $lead->getId()]);
    }

    /**
     * @Route("/exchange/trade/reject/{id}", name="app_exchange_trade_reject", methods={"GET"})
     *
     * @param Trade                    $trade
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return Response
     */
    public function rejectBuyAction(Trade $trade, EventDispatcherInterface $eventDispatcher): Response
    {
        $lead = $trade->getLead();

        if (!$this->isGranted(TradeVoter::REJECT, $trade)) {
            $this->addFlash('error', 'У Вас нет прав для отказа от указанной сделки');

            return $this->redirectToRoute('app_exchange_show_lead', ['id' => $lead->getId()]);
        }

        try {

            $this->tradeManager->finishReject($trade);
            $eventDispatcher->dispatch(LeadEvent::BLOCK_BY_REJECT, new LeadEvent($lead));

            $this->addFlash('success', 'Покупка успешно отменена');

        } catch(\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_exchange_show_lead', ['id' => $lead->getId()]);
    }

    /**
     * @return Response
     *
     * @throws \Exception
     */
    public function unsetLeadReserveModal(): Response
    {
        $user = $this->getUser();

        $lead = $this->entityManager->getRepository('AppBundle:Lead')
            ->getByUserAndReserved($user);

        if ($lead) {
            return $this->render('@App/Exchange/lead_reserved.html.twig', ['lead' => $lead]);
        }

        return new Response();
    }
}