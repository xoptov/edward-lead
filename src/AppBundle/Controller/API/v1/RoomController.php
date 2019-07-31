<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Entity\Room;
use AppBundle\Entity\Member;
use AppBundle\Event\RoomEvent;
use AppBundle\Security\Voter\RoomVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @Route("/api/v1")
 */
class RoomController extends Controller
{
    /**
     * @Route("/rooms", name="api_v1_rooms", methods={"GET"}, options={"_format": "json"})
     *
     * @return JsonResponse
     */
    public function getAllAction(): JsonResponse
    {
        $rooms = $this->getDoctrine()->getRepository(Room::class)
            ->getByMember($this->getUser());

        $result = [];

        /** @var Room $room */
        foreach ($rooms as $room) {
            $result[] = [
                'id' => $room->getId(),
                'name' => $room->getName(),
                'leadCriteria' => $room->getLeadCriteria(),
                'leadPrice' => $room->getLeadPrice(),
                'platformWarranty' => $room->isPlatformWarranty()
            ];
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/room/{room}/members", name="api_v1_room_members", methods={"GET"}, defaults={"_format":"json"})
     *
     * @param Room         $room
     * @param CacheManager $cacheManager
     *
     * @return JsonResponse
     */
    public function getMembersAction(Room $room, CacheManager $cacheManager): JsonResponse
    {
        if (!$this->isGranted(RoomVoter::VIEW, $room)) {
            return new JsonResponse(['error' => 'Нет прав на просмотр списка членов группы'], Response::HTTP_FORBIDDEN);
        }

        $members = $this->getDoctrine()->getRepository(Member::class)
            ->findBy(['room' => $room]);

        $result = [
            'companies' => [],
            'webmasters' => []
        ];

        foreach ($members as $member) {
            $user = $member->getUser();

            $item = [
                'id' => $member->getId(),
                'user' => [
                    'id' => $user->getId(),
                    'name' => $user->getName(),
                    'isOwner' => $room->isOwner($user),
                    'logotype' => null
                ]
            ];

            if ($user->isCompany() && $user->getCompany()->getLogotype()) {
                $logotype = $user->getCompany()->getLogotype();
                $item['user']['logotype'] = $cacheManager->getBrowserPath($logotype->getPath(), 'logotype_34x34');
            }

            if ($user->isWebmaster()) {
                $result['webmasters'][] = $item;
            } elseif ($user->isCompany()) {
                $result['companies'][] = $item;
            }
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/room/{room}/revoke/{member}", name="api_v1_room_revoke_member", methods={"DELETE"}, defaults={"_format":"json"})
     *
     * @param Room                   $room
     * @param Member                 $member
     * @param EntityManagerInterface $entityManager
     *
     * @return JsonResponse
     */
    public function deleteMemberAction(
        Room $room,
        Member $member,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        if (!$this->isGranted(RoomVoter::REVOKE_MEMBER, $room)) {
            return new JsonResponse(['error' => 'Нет прав на удаление пользователя'], Response::HTTP_FORBIDDEN);
        }

        $user = $member->getUser();

        $entityManager->remove($member);
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Отозвано участие у пользователя в комнате',
            'room' => $room->getId(),
            'user' => $user->getId()
        ]);
    }

    /**
     * @Route("/room/{room}/deactivate", name="api_v1_room_deactivate", methods={"GET"}, defaults={"_format":"json"})
     *
     * @param Room                     $room
     * @param EntityManagerInterface   $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return JsonResponse
     */
    public function getDeactivateAction(
        Room $room,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ): JsonResponse {
        if (!$this->isGranted(RoomVoter::DEACTIVATE, $room)) {
            return new JsonResponse(['error' => 'Нет прав для деактивации комнаты'], Response::HTTP_FORBIDDEN);
        }

        $room->setEnabled(false);

        $entityManager->flush();

        $eventDispatcher->dispatch(RoomEvent::DEACTIVATED, new RoomEvent($room));

        return new JsonResponse([
            'message' => 'Комната успешно деактивирована',
            'room' => $room->getId()
        ]);
    }

    /**
     * @Route("/room/send/invite", name="api_v1_send_invite", methods={"POST"}, defaults={"_format": "json"})
     *
     * @param Request                $request
     * @param \Swift_Mailer          $mailer
     * @param EntityManagerInterface $entityManager
     *
     * @return JsonResponse
     */
    public function postSendInviteAction(
        Request $request,
        \Swift_Mailer $mailer,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $formBuilder = $this->createFormBuilder(null, [
            'method' => Request::METHOD_POST,
            'csrf_protection' => false
        ]);

        $formBuilder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Необходимо указать email']),
                    new Email(['message' => 'Указал невалидный email']),
                ]
            ])
            ->add('token', HiddenType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Необходимо указать token'])
                ]
            ]);

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            $room = $entityManager->getRepository(Room::class)->findOneBy([
                'inviteToken' => $data['token']
            ]);

            if (!$room) {
                return new JsonResponse(['errors' => ['Невалидный токен приглашения']], Response::HTTP_BAD_REQUEST);
            }

            $content = $this->renderView('@App/v2/Room/invite_email.txt.twig', [
                'room' => $room,
                'inviteUrl' => $this->generateUrl('app_room_invite_confirm', ['token' => $data['token']], UrlGeneratorInterface::ABSOLUTE_URL)
            ]);

            $senderEmail = $this->getParameter('system_email');

            $message = new \Swift_Message('Приглашение в комнату', $content);
            $message
                ->setFrom($senderEmail)
                ->setTo($data['email']);

            $mailer->send($message);

            return new JsonResponse(['message' => 'Приглашение в комнату принято в очередь на отправку']);
        }

        $errors = [];

        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }
}