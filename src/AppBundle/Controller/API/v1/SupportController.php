<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Entity\User;
use AppBundle\Entity\Thread;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\MessageBundle\EntityManager\ThreadManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/api/v1/support")
 */
class SupportController extends Controller
{
    /**
     * @Route(name="api_v1_support_create", methods={"POST"})
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
}