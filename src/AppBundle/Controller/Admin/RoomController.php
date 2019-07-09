<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use AppBundle\Service\RoomManager;
use Symfony\Component\HttpFoundation\Request;
use Sonata\AdminBundle\Controller\CRUDController;

class RoomController extends CRUDController
{
    /**
     * @var RoomManager
     */
    private $roomManager;

    /**
     * @param RoomManager $roomManager
     */
    public function __construct(RoomManager $roomManager)
    {
        $this->roomManager = $roomManager;
    }

    /**
     * @inheritdoc
     */
    protected function preCreate(Request $request, $object)
    {
        /** @var User $owner */
        $owner = $object->getOwner();

        if ($owner) {
            $this->roomManager->joinInRoom($object, $owner);
        }
    }
}