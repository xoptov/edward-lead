<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @param EntityManagerInterface       $entityManager
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
    }

    /**
     * @param User $user
     * @param bool $flush
     *
     * @throws OptimisticLockException
     */
    public function updateUser(User $user, $flush = true)
    {
        if ($user->getPlainPassword()) {
            $encodedPassword = $this->encoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($encodedPassword);
        }

        $user->eraseCredentials();

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @return string
     */
    public function generateConfirmToken()
    {
        return sha1(time());
    }
}