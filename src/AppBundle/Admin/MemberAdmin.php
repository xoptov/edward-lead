<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class MemberAdmin extends AbstractAdmin
{
    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('id')
            ->add('room.id')
            ->add('room.name')
            ->add('user.id')
            ->add('user.name');
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->add('room.id')
            ->add('room.name')
            ->add('user.id')
            ->add('user.name')
            ->add('_action', null, [
                'actions' => [
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
            ->add('room', EntityType::class, [
                'class' => Room::class,
                'choice_label' => function(Room $room) {
                    return $room->getId() . ': ' . $room->getName();
                }
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => function(User $user) {
                    return $user->getId() . ': ' . $user->getName();
                }
            ])
        ;
    }

    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list', 'create', 'delete']);
    }
}