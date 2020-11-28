<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Entity\User;
use AppBundle\Entity\Trade;
use AppBundle\Entity\Account;
use AppBundle\Service\LeadManager;
use AppBundle\Repository\TradeRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/api/v1")
 */
class TradeController extends Controller
{
    /**
     * @var LeadManager
     */
    private $leadManager;

    /**
     * @param LeadManager $leadManager
     */
    public function __construct(LeadManager $leadManager)
    {
        $this->leadManager = $leadManager;
    }

    /**
     * @Route(
     *  "/trades",
     *  name="api_v1_trades",
     *  methods={"GET"},
     *  defaults={"_format"="json"}
     * )
     *
     * @return JsonResponse
     */
    public function getListAction(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var TradeRepository */
        $tradeRepository = $this->getDoctrine()->getRepository(Trade::class);

        if ($user->getOfficeCities()) {

            $trades = $tradeRepository->getByCitiesAndStatus(
                $user->getOfficeCities()->toArray(),
                Trade::STATUS_ACCEPTED
            );
        } else {
            $trades = $tradeRepository->findBy([
                'status' => Trade::STATUS_ACCEPTED
            ]);
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
}