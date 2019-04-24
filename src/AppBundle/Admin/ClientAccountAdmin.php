<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use AppBundle\Admin\Field\MoneyFieldDescription;

class ClientAccountAdmin extends AbstractAdmin
{
    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $balanceField = new MoneyFieldDescription();
        $balanceField->setName('balance');

        $filter
            ->add('id')
            ->add('user.name')
            ->add($balanceField)
            ->add('enabled');
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $list)
    {
        $balanceField = new MoneyFieldDescription();
        $balanceField->setName('balance');

        $list
            ->addIdentifier('id', 'number')
            ->add('user.name')
            ->add($balanceField, 'number')
            ->add('enabled');
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list']);
    }
}