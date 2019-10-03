<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use AppBundle\Entity\Account;
use AppBundle\Service\RoomManager;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use AppBundle\Admin\Field\MoneyFieldDescription;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class RoomAdmin extends AbstractAdmin
{
    /**
     * @var RoomManager
     */
    private $roomManager;

    /**
     * @param RoomManager $roomManager
     */
    public function setRoomManager(RoomManager $roomManager)
    {
        $this->roomManager = $roomManager;
    }

    /**
     * @param Room $object
     *
     * @throws \Exception
     */
    public function prePersist($object)
    {
        $owner = $object->getOwner();
        $this->roomManager->joinInRoom($object, $owner);
        $this->roomManager->updateInviteToken($object);
    }

    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $leadPrice = new MoneyFieldDescription();
        $leadPrice->setName('leadPrice');

        $filter
            ->add('id')
            ->add('owner.name')
            ->add('sphere')
            ->add('platformWarranty')
            ->add($leadPrice)
            ->add('enabled');
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $list)
    {
        $leadPrice = new MoneyFieldDescription();
        $leadPrice->setName('leadPrice');

        $list
            ->addIdentifier('id')
            ->add('name')
            ->add('sphere')
            ->add('leadCriteria')
            ->add('platformWarranty')
            ->add($leadPrice)
            ->add('buyerFee', null, [
                'template' => '@App/CRUD/list_buyer_fee.html.twig'
            ])
            ->add('hideFee')
            ->add('enabled')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => []
                ]
            ]);
    }

    /**
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('name')
            ->add('owner', ModelAutocompleteType::class, [
                'property' => 'name',
                'to_string_callback' => function(User $entity) {
                    return $entity->getId() . ' : ' . $entity->getName();
                }
            ])
            ->add('sphere')
            ->add('leadCriteria')
            ->add('platformWarranty', null, [
                'required' => false
            ])
            ->add('leadPrice', MoneyType::class, [
                'currency' => 'RUB',
                'divisor' => Account::DIVISOR
            ])
            ->add('buyerFee')
            ->add('hideFee')
            ->add('enabled');
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $show)
    {
        $leadPrice = new MoneyFieldDescription();
        $leadPrice->setName('leadPrice');

        $show
            ->add('name')
            ->add('owner.name')
            ->add('sphere')
            ->add('leadCriteria')
            ->add('platformWarranty')
            ->add($leadPrice)
            ->add('buyerFee', null, [
                'template' => '@App/CRUD/show_buyer_fee.html.twig'
            ])
            ->add('hideFee')
            ->add('enabled');
    }

    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['create', 'edit', 'list', 'show']);
    }
}