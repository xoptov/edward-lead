<?php


namespace NotificationBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use NotificationBundle\Entity\UserWithWebPushInterface;
use NotificationBundle\Exception\ValidationTelegramHookException;
use NotificationBundle\Exception\WebPushTokenHandlerException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class WebPushTokenHandler
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
     * @var Security
     */
    private $security;

    /**
     * WebPushTokenHandler constructor.
     *
     * @param ValidatorInterface     $validator
     * @param EntityManagerInterface $entityManager
     * @param Security               $security
     */
    public function __construct(ValidatorInterface $validator, EntityManagerInterface $entityManager, Security $security)
    {
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function handle(array $data): void
    {
        $this->validate($data);

        /** @var UserWithWebPushInterface $user */
        $user = $this->security->getUser();

        $user->setWebPushToken($data['token']);

        $this->entityManager->flush();

    }

    /**
     * @param array $data
     *
     * @throws WebPushTokenHandlerException
     */
    private function validate(array $data): void
    {
        $constraint = $this->getConstraints();
        $violations = $this->validator->validate($data, $constraint);

        if (count($violations) > 0) {
            throw new WebPushTokenHandlerException($violations);
        }
    }

    /**
     * @return Collection
     */
    private function getConstraints()
    {
        return new Collection([
            'token' => new NotBlank(),
        ]);
    }
}