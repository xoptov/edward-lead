<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ReferrerReward;
use AppBundle\Entity\User;
use AppBundle\Service\ReferrerManager;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/referral")
 */
class ReferralController extends Controller
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route(name="app_referral_index", methods={"GET"})
     *
     * @param ReferrerManager $referrerManager
     *
     * @return Response
     */
    public function indexAction(ReferrerManager $referrerManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $token = $referrerManager->getReferrerToken($user);
        $earned = 0;

        $rewards = $this->entityManager
            ->getRepository(ReferrerReward::class)
            ->findBy(['referrer' => $user]);

        foreach ($rewards as $reward) {
            $earned += $reward->getAmount();
        }

        try {
            $referralCount = $this->entityManager
                ->getRepository(User::class)
                ->getReferralCount($user);
        } catch (DBALException $e) {
            $referralCount = 0;
        }

        return $this->render('@App/Referral/index.html.twig', [
            'referralCount' => $referralCount,
            'earned' => $earned,
            'referrerToken' => $token
        ]);
    }
}
