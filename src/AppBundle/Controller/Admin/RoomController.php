<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Lead;
use AppBundle\Entity\Room;
use AppBundle\Entity\Member;
use AppBundle\Service\RoomManager;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var RoomManager
     */
    private $roomManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @param RoomManager            $roomManager
     */
    public function __construct(
        RoomManager $roomManager,
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
        $this->roomManager = $roomManager;
    }

    /**
     * @inheritdoc
     */
    protected function preCreate(Request $request, $object)
    {
        /** @var Room $object */
        $owner = $object->getOwner();

        if ($owner) {
            $this->roomManager->joinInRoom($object, $owner);
        }
    }

    /**
     * @inheritdoc
     */
    protected function preShow(Request $request, $object)
    {
        $this->admin->setSubject($object);

        $fields = $this->admin->getShow();
        \assert($fields instanceof FieldDescriptionCollection);

        $members = $this->entityManager->getRepository(Member::class)
            ->getByRooms([$object]);

        $leads = $this->entityManager->getRepository(Lead::class)
            ->findBy(['room' => $object], ['createdAt' => 'DESC', 'updatedAt' => 'DESC']);

        return $this->renderWithExtraParams('@App/CRUD/room_show.html.twig', [
            'action' => 'show',
            'object' => $object,
            'elements' => $fields,
            'members' => $members,
            'leads' => $leads
        ], null);
    }
}