<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use AppBundle\Admin\Field\MoneyFieldDescription;
use AppBundle\Admin\Field\AccountTypeFieldDescription;

class AccountAdmin extends AbstractAdmin
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
            ->add('description')
            ->add($balanceField)
            ->add('enabled');
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $list)
    {
        $typeField = new AccountTypeFieldDescription();
        $typeField->setName('type');

        $balanceField = new MoneyFieldDescription();
        $balanceField->setName('balance');

        $list
            ->addIdentifier('id', 'number')
            ->add($typeField, null, ['virtual_field' => true])
            ->add('description')
            ->add($balanceField, 'number')
            ->add('enabled')
            ->add('updatedAt', 'datetime', ['format' => 'd.m.Y H:i:m'])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'toggle' => ['template' => '@App/CRUD/list__action_enabled_toggle.html.twig'],
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
            ->add('description')
            ->add('enabled');
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show)
    {
        $typeField = new AccountTypeFieldDescription();
        $typeField->setName('type');

        $balanceField = new MoneyFieldDescription();
        $balanceField->setName('balance');

        $show
            ->add('id')
            ->add($typeField, null, ['virtual_field' => true])
            ->add('description')
            ->add($balanceField, 'number')
            ->add('enabled')
            ->add('updatedAt', 'datetime', ['format' => 'd.m.Y H:i:m']);
    }

    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->clearExcept([
                'create',
                'list',
                'show',
                'edit',
                'delete'
            ])
            ->add('toggle', $this->getRouterIdParameter().'/toggle');
    }
}