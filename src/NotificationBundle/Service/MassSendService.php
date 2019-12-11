<?php

namespace NotificationBundle\Service;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use NotificationBundle\Client\InternalClient;
use NotificationBundle\Entity\Notification;
use NotificationBundle\Exception\ValidationTelegramHookException;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MassSendService
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
     * @var InternalClient
     */
    private $client;

    /**
     * TelegramHookHandler constructor.
     *
     * @param ValidatorInterface     $validator
     * @param EntityManagerInterface $entityManager
     * @param InternalClient         $client
     */
    public function __construct(ValidatorInterface $validator, EntityManagerInterface $entityManager, InternalClient $client)
    {
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->client = $client;
    }

    /**
     * @param array $data
     *
     * @throws ValidationTelegramHookException
     */
    public function handle(array $data): void
    {
        $this->validate($data);

        /** @var User[] $users */
        $users = $this->entityManager->getRepository(User::class)->findAll();

        foreach ($users as $user){

            $notification = new Notification($user, $data['message']);

            $this->client->send($notification);

        }

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
            'message' => new NotBlank(),
        ]);
    }

}