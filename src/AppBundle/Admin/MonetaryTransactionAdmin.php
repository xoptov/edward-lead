<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use AppBundle\Admin\Field\MoneyFieldDescription;

class MonetaryTransactionAdmin extends AbstractAdmin
{
    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $amountField = new MoneyFieldDescription();
        $amountField->setName('amount');

        $datagridMapper
            ->add('id')
            ->add('account.id')
            ->add($amountField)
            ->add('processed')
            ->add('createdAt');
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $amountField = new MoneyFieldDescription();
        $amountField->setName('amount');

        $listMapper
            ->addIdentifier('id')
            ->add('account.id')
            ->add('operation.description')
            ->add($amountField)
            ->add('processed')
            ->add('createdAt');
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $amountField = new MoneyFieldDescription();
        $amountField->setName('amount');

        $showMapper
            ->add('id')
            ->add('account.id')
            ->add('operation.description')
            ->add($amountField)
            ->add('processed')
            ->add('createdAt');
    }

    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list', 'show']);
    }
}
