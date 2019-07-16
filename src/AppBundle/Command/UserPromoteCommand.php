<?php

namespace AppBundle\Command;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserPromoteCommand extends Command
{
    const STATUS_OK = 0;
    const STATUS_USER_NOT_FOUND = 1;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @param null|string $name
     */
    public function __construct(EntityManagerInterface $entityManager, ?string $name = null)
    {
        parent::__construct($name);

        $this->entityManager = $entityManager;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('app:user:promote')
            ->setDescription('Команда для назначения пользователю роли')
            ->addArgument('email', InputArgument::REQUIRED, 'Email пользователя для назначения новой роли')
            ->addArgument('role', InputArgument::REQUIRED, 'Роль для назначения');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $role = $input->getArgument('role');

        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if (!$user) {
            $output->writeln('Пользователь с указанным email не найден');

            return self::STATUS_USER_NOT_FOUND;
        }

        $user->addRole($role);
        $this->entityManager->flush();

        $output->writeln('Пользователю с email '.$email.' добавлена роль '.$role);

        return self::STATUS_OK;
    }
}