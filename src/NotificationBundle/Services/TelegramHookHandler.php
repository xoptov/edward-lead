<?php


namespace NotificationBundle\Services;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use NotificationBundle\Entity\UserNotificationTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TelegramHookHandler
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * TelegramHookHandler constructor.
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }

    /**
     * @param array $data
     * @throws Exception
     */
    public function handle(array $data)
    {
        $this->validate($data);

        $chantId = $data['message']['chat']['id'];
        $tokenMessage = $data['message']['text'];
        $token = explode(" ", $tokenMessage)[1];

        $user = $this->entityManager->getRepository(User::class)->findOneBy(["telegramAuthToken" => $token]);

        if (!$user instanceof User) {
            throw new Exception('No user found with token ' . $token);
        }

        $user->setTelegramChatId($chantId);

        $this->entityManager->flush();
    }

    /**
     * @param array $data
     * @throws Exception
     */
    private function validate(array $data): void
    {

        $constraint = $this->getConstraints();
        $violations = $this->validator->validate($data, $constraint);

        if (count($violations) > 0) {
            throw new Exception($violations);
        }
    }

    /**
     * @return Assert\Collection
     */
    private function getConstraints()
    {
        return new Assert\Collection([
            'message' => new Assert\Collection([
                'chat' => new Assert\Collection([
                    'id' => new Assert\NotBlank()
                ]),
                'text' => new Assert\NotBlank()
            ]),
        ]);
    }
}