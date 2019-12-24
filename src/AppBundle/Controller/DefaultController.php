<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnexpectedResultException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="app_index", methods={"GET"})
     *
     * @param Request                $request
     * @param ValidatorInterface     $validator
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    public function indexAction(
        Request $request,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager
    ): Response {

        // Этот чудный код делает редирект на реальный роут для инвайта.

        $inviteShortToken = $request->query->get('i');

        if ($inviteShortToken) {

            $constraint = new Length([
                'min' => 5,
                'minMessage' => 'Токен приглашения не может быть короче 5 символов',
                'max' => 32,
                'maxMessage' => 'Токен приглашения не может быть длиннее 32 символов'
            ]);

            $violations = $validator->validate($inviteShortToken, [$constraint]);

            if ($violations->count()) {
                /** @var ConstraintViolation $violation */
                foreach ($violations as $violation) {
                    $this->addFlash('error', $violation->getMessage());
                }
            } else {
                /** @var RoomRepository $roomRepository */
                $roomRepository = $entityManager->getRepository(Room::class);

                try {
                    $room = $roomRepository
                        ->getByInviteShortToken($inviteShortToken);

                    return $this->redirectToRoute('app_room_invite_confirm', ['token' => $room->getInviteToken()]);

                } catch (UnexpectedResultException $e) {
                    return $this->redirectToRoute('app_room_invite_invalid');
                }
            }
        }

        // Конец чудного кода для обработки инвайта в комнату.

        /** @var User $user */
        $user = $this->getUser();

        if ($user->isWebmaster()) {
            return $this->redirectToRoute('app_user_dashboard');
        } elseif ($user->isAdvertiser()) {
            return $this->redirectToRoute('app_room_list');
        }

        return $this->redirectToRoute('app_user_select_role');
    }
}
