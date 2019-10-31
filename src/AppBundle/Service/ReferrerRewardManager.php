<?php

namespace AppBundle\Service;

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
        $this->rewardInterest = $rewardInterest;
    }

    /**
     * @param Trade     $trade
     * @param bool|null $flush
     *
     * @return ReferrerReward[]|null
     *
     * @throws OperationException
     */
    public function createRewardsForTrade(Trade $trade, bool $flush = true): ?array
    {
        if ($this->rewardInterest <= 0) {
            return null;
        }

        if ($trade->isProcessed()) {
            throw new OperationException($trade, 'Торговая операция уже обработана');
        }

        $feesAmount = $this->feesManager->getCommissionForBuyingLead(
            $trade->getLead()
        );

        if (empty($feesAmount)) {
            return null; // Нет вознаграждений так как нет комиссии системы
        }

        $buyer = $trade->getBuyer();
        $seller = $trade->getSeller();

        if (!$buyer->hasReferrer() && !$seller->hasReferrer()) {
            return null;
        }

        $rewardAmount = intval(floor($feesAmount * $this->rewardInterest / 100));

        if ($buyer->hasReferrer() && $seller->hasReferrer()) {
            $rewardAmount = intval(floor($feesAmount / 2));
        }

        if ($rewardAmount === 0) {
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