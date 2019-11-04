<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;

class UserController extends CRUDController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritdoc
     */
    protected function preShow(Request $request, $object)
    {
        $fields = $this->admin->getShow();

        $referrals = $this->entityManager
            ->getRepository(User::class)
            ->findBy(['referrer' => $object]);

        return $this->renderWithExtraParams('@App/CRUD/show_user.html.twig', [
            'action' => 'show',
            'object' => $object,
            'elements' => $fields,
            'referrals' => $referrals
        ], null);
    }
}