<?php

namespace AppBundle\Service;

use AppBundle\Util\Math;
use AppBundle\Entity\User;
use AppBundle\Entity\Trade;
use AppBundle\Entity\Operation;
use AppBundle\Entity\ReferrerReward;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Exception\OperationException;

class ReferrerRewardManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var FeesManager
     */
    private $feesManager;

    /**
     * @var float
     */
    private $rewardInterest;

    /**
     * @param EntityManagerInterface $entityManager
     * @param FeesManager            $feesManager
     * @param float                  $rewardInterest
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        FeesManager $feesManager,
        float $rewardInterest
    ) {
        $this->entityManager = $entityManager;
        $this->feesManager = $feesManager;

        if ($rewardInterest <= 0.0 || $rewardInterest >= 100.0) {
            $this->rewardInterest = 0.0;
        } else {
            $this->rewardInterest = $rewardInterest;
        }
    }

    /**
     * @param Trade $trade
     * @param bool  $flush
     *
     * @return ReferrerReward[]|null
     *
     * @throws OperationException
     */
    public function createRewardsForTrade(
        Trade $trade,
        bool $flush = true
    ): ?array {

        if (0 >= $this->rewardInterest) {
            return null;
        }

        if ($trade->isProcessed()) {
            throw new OperationException($trade, 'Торговая операция уже обработана');
        }

        $interest = $this->feesManager->getCommissionForBuyingLead($trade->getLead());

        if (empty($interest)) {
            return null;
        }

        $feesAmount = Math::calculateByInterest($trade->getAmount(), $interest);

        if (0 >= $feesAmount) {
            return null;
        }

        $buyer = $trade->getBuyer();
        $seller = $trade->getSeller();

        if (!$buyer->hasReferrer() && !$seller->hasReferrer()) {
            return null;
        }

        $rewardAmount = intval(ceil($feesAmount * $this->rewardInterest / 100));

        if ($buyer->hasReferrer() && $seller->hasReferrer()) {
            $rewardAmount = intval(ceil($feesAmount / 2));
        }

        if (0 >= $rewardAmount) {
            return null; // Вознагрождение настолоко мало что его нет смысла учитывать.
        }

        $rewards = [];

        if ($buyer->hasReferrer()) {
            $reward = $this->createReward($trade, $buyer->getReferrer(), $rewardAmount);
            $rewards[] = $reward;
        }

        if ($seller->hasReferrer()) {
            $reward = $this->createReward($trade, $seller->getReferrer(), $rewardAmount);
            $rewards[] = $reward;
        }

        if ($flush) {
            $this->entityManager->flush();
        }

        return $rewards;
    }

    /**
     * @param Operation $operation
     * @param User      $referrer
     * @param int       $amount
     * @param bool      $persist
     *
     * @return ReferrerReward
     */
    public function createReward(
        Operation $operation,
        User $referrer,
        int $amount,
        bool $persist = true
    ): ReferrerReward {

        $reward = new ReferrerReward();
        $reward
            ->setOperation($operation)
            ->setReferrer($referrer)
            ->setDescription('Вознаграждение за сделку рефералла')
            ->setAmount($amount);

        if ($persist) {
            $this->entityManager->persist($reward);
        }

        return $reward;
    }
}