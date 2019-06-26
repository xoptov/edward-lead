<?php

namespace AppBundle\Admin;

use AppBundle\Entity\UserDeleteRequest;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class UserDeleteRequestAdmin extends AbstractAdmin
{
    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->add('user.id')
            ->add('status', 'choice', [
                'choices' => [
                    UserDeleteRequest::STATUS_NEW      => 'Новый',
                    UserDeleteRequest::STATUS_ACCEPTED => 'Принят',
                    UserDeleteRequest::STATUS_REJECTED => 'Отменен'
                ]
            ])
            ->add('_action', null, [
                'actions' => [
                    'delete' => [],
                    'accept' => ['template' => '@App/CRUD/list__action_accept.html.twig'],
                    'reject' => ['template' => '@App/CRUD/list__action_reject.html.twig']
                ]
            ]);
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list', 'delete'])
            ->add('accept', $this->getRouterIdParameter().'/accept')
            ->add('reject', $this->getRouterIdParameter().'/reject');
    }
}