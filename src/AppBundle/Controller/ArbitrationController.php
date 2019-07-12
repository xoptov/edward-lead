<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Thread;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class ArbitrationController extends Controller
{
    /**
     * @Route("/arbitration", name="app_arbitration", methods={"GET"})
     */
    public function default()
    {
        $provider = $this->get('fos_message.provider');

        $inboxThreads = $provider->getInboxThreads();
        $sentThreads = $provider->getSentThreads();

        $threads = array_merge($inboxThreads, $sentThreads);

        usort($threads, function (Thread $a, Thread $b) {
            if ($a->getCreatedAt() == $b->getCreatedAt()) {
                return 0;
            }
            return ($a->getCreatedAt() > $b->getCreatedAt()) ? -1 : 1;
        });

        $user = $this->getUser();

        $openedThreads = array_filter($threads, function ($thread) use ($user) {
            /** @var Thread $thread */
            if ($thread->getStatus() != Thread::STATUS_CLOSED && ! $thread->isReadByParticipant($user)) {
                return true;
            }
            return false;
        });

        $archiveThreads = array_filter($threads, function ($thread) use ($user) {
            /** @var Thread $thread */
            if ($thread->getStatus() == Thread::STATUS_CLOSED && $thread->isReadByParticipant($user)) {
                return true;
            }
            return false;
        });

        return $this->render("@App/Arbitration/default.html.twig", [
            'openedThreads' => $openedThreads,
            'archiveThreads' => $archiveThreads
        ]);
    }
}