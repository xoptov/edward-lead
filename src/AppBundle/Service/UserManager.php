<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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
    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $encoder
    ) {
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
    }

    /**
     * @param User $user
     * @param bool $flush
     */
    public function updateUser(User $user, bool $flush = true)
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
     * @param User $user
     */
    public function updateResetToken(User $user): void
    {
        $token = $this->generateToken();
        $user->setResetToken($token);
    }

    /**
     * @param User $user
     */
    public function updateAccessToken(User $user): void
    {
        $token = $this->generateToken();
        $user->setToken($token);
    }

    /**
     * @return string
     */
    private function generateToken(): string
    {
        return sha1(time());
    }
}