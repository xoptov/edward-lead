<?php


namespace NotificationBundle\Service;


use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use NotificationBundle\Channels\EmailChannel;
use NotificationBundle\Channels\SmsChannel;
use NotificationBundle\Channels\TelegramChannel;
use NotificationBundle\Channels\WebPushChannel;
use NotificationBundle\Constants\Cases;
use NotificationBundle\Entity\NotificationConfiguration;
use NotificationBundle\Repository\NotificationConfigurationRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DisableNotificationService
{
    /**
     * @var notificationConfigurationRepository
     */
    private $notificationConfigurationRepository;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * NotificationController constructor.
     *
     * @param NotificationConfigurationRepository $notificationConfigurationRepository
     * @param Security                            $security
     * @param ValidatorInterface                  $validator
     * @param EntityManager                       $entityManager
     */
    public function __construct(
        NotificationConfigurationRepository $notificationConfigurationRepository,
        Security $security,
        ValidatorInterface $validator,
        EntityManager $entityManager
    )
    {
        $this->notificationConfigurationRepository = $notificationConfigurationRepository;
        $this->security = $security;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }

    /**
     * @param array $data
     *
     * @return NotificationConfiguration
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function handle(array $data) : NotificationConfiguration
    {
        $this->validate($data);

        /** @var User $currentUser */
        $currentUser = $this->security->getUser();

        $case = $data['case'];
        $channel = $data['channel'];
        $isDisabled = $data['isDisabled'];

        $configuration = $this->notificationConfigurationRepository->findByConfigurations(
            $case,
            $channel,
            $currentUser
        );

        if ($configuration) {

            $configuration->setDisabled($isDisabled);
            $this->entityManager->persist($configuration);

        } else {

            $configuration = new NotificationConfiguration();
            $configuration->setUser($currentUser);
            $configuration->setCase($case);
            $configuration->setChannel($channel);
            $configuration->setDisabled($isDisabled);
        }

        $this->entityManager->persist($configuration);
        $this->entityManager->flush();

        return $configuration;
    }

    /**
     * @param array $data
     *
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
     * @return Collection
     */
    private function getConstraints()
    {
        return new Collection([
            'isDisabled' => [
                new NotBlank()
            ],
            'case' => [
                new NotBlank(),
                new Choice([
                    'choices' => [Cases::getCases()],
                ])
            ],
            'channel' => [
                new NotBlank(),
                new Choice([
                    'choices' => [
                        EmailChannel::NAME,
                        SmsChannel::NAME,
                        TelegramChannel::NAME,
                        WebPushChannel::NAME
                    ],
                ])
            ]
        ]);
    }
}