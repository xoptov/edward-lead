<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Entity\User;
use AppBundle\Entity\Thread;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\MessageBundle\Sender\Sender;
use Doctrine\ORM\EntityManagerInterface;
use FOS\MessageBundle\Composer\Composer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SupportController extends Controller
{
    /**
     * @var Composer
     */
    private $fosComposer;

    /**
     * @var Sender
     */
    private $fosSender;

    /**
     * @param Composer $fosComposer
     * @param Sender   $fosSender
     */
    public function __construct(
        Composer $fosComposer,
        Sender $fosSender
    ) {
        $this->fosComposer = $fosComposer;
        $this->fosSender = $fosSender;
    }

    /**
     * @Route("/api/v1/support", name="api_v1_support_create", methods={"POST"})
     *
     * @param EntityManagerInterface $entityManager
     * @param CacheManager           $cacheManager
     *
     * @return JsonResponse
     */
    public function createAction(
        EntityManagerInterface $entityManager,
        CacheManager $cacheManager
    ): JsonResponse {

        /** @var User $user */
        $user = $this->getUser();

        $admins = $entityManager->getRepository(User::class)
            ->getAdmins();

        if (empty($admins)) {
            return new JsonResponse(['error' => 'В системе нет администраторов'], Response::HTTP_BAD_REQUEST);
        }


        $threadBuilder = $this->fosComposer->newThread();
        $threadBuilder->setSubject('Обращение в техподдержку');
        $threadBuilder->setSender($user);
        $threadBuilder->setBody('Прошу помочь в решении технического вопроса связанного с работой системы');
        $threadBuilder->addRecipients(new ArrayCollection($admins));

        $message = $threadBuilder->getMessage();

        /** @var Thread $thread */
        $thread = $message->getThread();
        $thread
            ->setStatus(Thread::STATUS_NEW)
            ->setTypeAppeal(Thread::TYPE_SUPPORT);

        $this->fosSender->send($message);

        unset($threadBuilder, $admins);

        $logotype = null;

        if ($user->isCompany() && $user->hasCompany() && $user->getCompany()->getLogotype()) {
            $logotypeImage = $user->getCompany()->getLogotype();
            $logotype = $cacheManager->getBrowserPath($logotypeImage->getPath(), 'logotype_26x26');
        }

        $result = [
            'id' => $thread->getId(),
            'lead' => null,
            'date' => $thread->getCreatedAt()->format('d.m.Y'),
            'status' => $thread->getStatus(),
            'type' => $thread->getTypeAppeal(),
            'thread' => 'open',
            'messages' => [
                [
                    'target_in' => false,
                    'target_out' => true,
                    'sender' => 'Ваше сообщение',
                    'body' => $message->getBody(),
                    'time' => $message->getCreatedAt()->format('d.m.Y H:i'),
                    'logotype' => $logotype,
                    'images'=> []
                ]
            ]
        ];

        return new JsonResponse($result);
    }
}