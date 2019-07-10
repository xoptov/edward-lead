<?php

namespace AppBundle\Admin;

use AppBundle\Entity\User;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use AppBundle\Admin\Field\MoneyFieldDescription;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Form\Type\DataTransformer\MoneyTransformer;

class RoomAdmin extends AbstractAdmin
{
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
            ->add('owner', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name'
            ])
            ->add('sphere')
            ->add('leadCriteria')
            ->add('platformWarranty')
            ->add('leadPrice')
            ->add('enabled');

        $form->get('leadPrice')->addViewTransformer(new MoneyTransformer());
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
            ->add('enabled');
    }

    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['create', 'edit', 'delete', 'list', 'show']);
    }
}