<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Entity\User;
use AppBundle\Entity\Thread;
use Doctrine\ORM\EntityManagerInterface;
use FOS\MessageBundle\Composer\ComposerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\MessageBundle\EntityManager\ThreadManager;
use FOS\MessageBundle\Sender\SenderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\MessageBundle\MessageBuilder\NewThreadMessageBuilder;

/**
 * @Route("/api/v1")
 */
class SupportController extends Controller
{
    /**
     * @Route("/support", name="api_v1_support_create", methods={"POST"})
     *
     * @param ThreadManager          $threadManager
     * @param EntityManagerInterface $entityManager
     *
     * @return JsonResponse
     */
    public function createAction(
        ThreadManager $threadManager,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();

        $admins = $entityManager->getRepository(User::class)
            ->getAdmins();

        if (empty($admins)) {
            return new JsonResponse(
                ['В системе нет администраторов'],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $lastThread = $entityManager->getRepository(Thread::class)
                ->getLastSupportThreadByCreatorAndTimeBound($user, new \DateTime('-1 minute'));
        } catch (\Exception $e) {
            return new JsonResponse(
                ['Произошла ошибка создания обращения'],
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($lastThread) {
            return new JsonResponse(
                ['Вы создали обращение меньше минуты назад'],
                Response::HTTP_BAD_REQUEST
            );
        }

        /** @var Thread $thread */
        $thread = $threadManager->createThread();
        $thread->setCreatedBy($user);
        $thread->setSubject('Обращение в техподдержку');
        $thread->setTypeAppeal(Thread::TYPE_SUPPORT);
        $thread->setStatus(Thread::STATUS_NEW);
        $thread->setIsSpam(false);
        $thread->setCreatedAt(new \DateTime());
        $thread->addParticipant($user);

        foreach ($admins as $admin) {
            $thread->addParticipant($admin);
        }

        $threadManager->saveThread($thread);

        $result = [
            'id' => $thread->getId(),
            'lead' => null,
            'date' => $thread->getCreatedAt()->format('d.m.Y'),
            'status' => $thread->getStatus(),
            'type' => $thread->getTypeAppeal(),
            'thread' => 'open',
            'messages' => []
        ];

        return new JsonResponse($result);
    }

    /**
     * @Route("/offer-request", name="api_v1_offer_request", methods={"GET"})
     * 
     * @param ComposerInterface      $composer
     * @param SenderInterface        $sender
     * @param EntityManagerInterface $entityManager
     * 
     * @return JsonResponse
     */
    public function offerRequestAction(
        ComposerInterface $composer,
        SenderInterface $sender,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();

        if ($user->isCompany()) {
            $messageBody = 'Запрос на создание оффера';
        } elseif ($user->isWebmaster()) {
            $messageBody = 'Запрос на подбор рекламодателей';
        } else {
            return new JsonResponse(
                ['Только рекламодатели или вэбмастеры могут делать запрос на создание офера'],
                Response::HTTP_FORBIDDEN
            );
        }

        $admins = $entityManager->getRepository(User::class)->getAdmins();

        if (empty($admins)) {
            return new JsonResponse(['В системе нет администраторов'], Response::HTTP_BAD_REQUEST);
        }

        /** @var NewThreadMessageBuilder $threadBuilder */
        $threadBuilder = $composer->newThread();
        $threadBuilder
            ->setSubject('Новый запрос на оффер')
            ->setSender($user)
            ->setBody($messageBody);

        foreach ($admins as $admin) {
            $threadBuilder->addRecipient($admin);
        }

        $message = $threadBuilder->getMessage();
        $sender->send($message);

        return new JsonResponse(['id' => $message->getId()]);
    }
}