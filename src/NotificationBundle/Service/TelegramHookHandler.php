<?php

namespace NotificationBundle\Service;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use NotificationBundle\Exception\NoUserWithTelegramTokenException;
use NotificationBundle\Exception\ValidationTelegramHookException;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
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
     *
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
     * @throws NoUserWithTelegramTokenException
     * @throws ValidationTelegramHookException
     */
    public function handle(array $data)
    {
        $this->validate($data);

        $chantId = $data['message']['chat']['id'];
        $tokenMessage = $data['message']['text'];
        $token = explode(" ", $tokenMessage)[1];

        $user = $this->entityManager->getRepository(User::class)->findOneBy(["telegramAuthToken" => $token]);

        if (!$user instanceof User) {
            throw new NoUserWithTelegramTokenException('No user found with token ' . $token);
        }

        $user->setTelegramChatId($chantId);

        $this->entityManager->flush();
    }

    /**
     * @param array $data
     * @throws ValidationTelegramHookException
     */
    private function validate(array $data): void
    {
        $constraint = $this->getConstraints();
        $violations = $this->validator->validate($data, $constraint);

        if (count($violations) > 0) {
            throw new ValidationTelegramHookException($violations);
        }
    }

    /**
     * @return Collection
     */
    private function getConstraints()
    {
        return new Collection([
            'message' => new Collection([
                'chat' => new Collection([
                    'id' => new NotBlank()
                ]),
                'text' => new NotBlank()
            ]),
        ]);
    }
}