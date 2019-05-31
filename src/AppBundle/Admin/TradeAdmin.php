<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class TradeAdmin extends AbstractAdmin
{
    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->add('buyer.name')
            ->add('seller.name')
            ->add('lead.name')
            ->add('status', 'choice', [
                'choices' => [
                    0 => 'New',
                    1 => 'Accepted',
                    2 => 'Rejected'
                ],
                'catalogue' => 'messages'
            ])
            ->add('_action', null, [
                'actions' => [
                    'reject' => ['template' => '@App/CRUD/list__action_reject.html.twig']
                ]
            ]);
    }

    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list'])
            ->add('reject', $this->getRouterIdParameter().'/reject');
    }
}