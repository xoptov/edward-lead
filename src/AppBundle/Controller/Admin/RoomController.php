<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Lead;
use AppBundle\Entity\Member;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Repository\MemberRepository;
use Symfony\Component\HttpFoundation\Request;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Admin\FieldDescriptionCollection;

class RoomController extends CRUDController
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
        $this->admin->setSubject($object);

        $fields = $this->admin->getShow();
        \assert($fields instanceof FieldDescriptionCollection);

        /** @var MemberRepository */
        $memberRepository = $this->entityManager->getRepository(Member::class);

        $members = $memberRepository
            ->getByRooms([$object]);

        $leads = $this->entityManager->getRepository(Lead::class)
            ->findBy(['room' => $object], ['createdAt' => 'DESC', 'updatedAt' => 'DESC']);

        return $this->renderWithExtraParams('@App/CRUD/show_room.html.twig', [
            'action' => 'show',
            'object' => $object,
            'elements' => $fields,
            'members' => $members,
            'leads' => $leads
        ], null);
    }
}
