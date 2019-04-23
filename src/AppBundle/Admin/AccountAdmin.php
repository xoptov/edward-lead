<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
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
        $filter
            ->add('id')
            ->add('description')
            ->add('balance')
            ->add('enabled');
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $list)
    {
        $balanceField = new MoneyFieldDescription();
        $balanceField->setName('balance');

        $typeField = new AccountTypeFieldDescription();
        $typeField->setName('type');

        $list
            ->add('id')
            ->add($typeField, null, ['virtual_field' => true])
            ->add('description')
            ->add($balanceField, 'number')
            ->add('enabled')
            ->add('_action', null, [
                'actions' => [
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
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->clearExcept(['create', 'list', 'edit', 'delete'])
            ->add('toggle', $this->getRouterIdParameter().'/toggle');
    }
}