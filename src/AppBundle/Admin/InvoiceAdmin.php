<?php

namespace AppBundle\Admin;

use AppBundle\Entity\User;
use AppBundle\Entity\Invoice;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use AppBundle\Admin\Field\MoneyFieldDescription;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Form\Type\DataTransformer\MoneyTransformer;

class InvoiceAdmin extends AbstractAdmin
{
    private $statuses = [
        Invoice::STATUS_NEW => 'new',
        Invoice::STATUS_DONE => 'done',
        Invoice::STATUS_CANCELED => 'canceled'
    ];

    /**
     * @inheritdoc
     */
    public function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('id')
            ->add('user.name')
            ->add('user.email')
            ->add('user.phone')
            ->add('description')
            ->add('amount');
    }

    /**
     * @inheritdoc
     */
    public function configureFormFields(FormMapper $form)
    {
        $form
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name'
            ])
            ->add('description')
            ->add('amount', 'number');

        $form->get('amount')
            ->addModelTransformer(new MoneyTransformer());
    }

    /**
     * @inheritdoc
     */
    public function configureListFields(ListMapper $list)
    {
        $amountField = new MoneyFieldDescription();
        $amountField->setName('amount');

        $list
            ->addIdentifier('id', 'number')
            ->add('user.name')
            ->add('user.email')
            ->add('user.phone')
            ->add('description')
            ->add($amountField, 'number')
            ->add('status', 'choice', [
                'choices' => $this->statuses,
                'catalogue' => 'messages',
                'template' => '@App/CRUD/list_status.html.twig'
            ])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'cancel' => ['template' => '@App/CRUD/list__action_cancel.html.twig'],
                    'process' => ['template' => '@App/CRUD/list__action_process.html.twig']
                ]
            ]);
    }

    public function configureShowFields(ShowMapper $show)
    {
        $amountField = new MoneyFieldDescription();
        $amountField->setName('amount');

        $show
            ->add('id', 'number')
            ->add('user.name')
            ->add('user.email')
            ->add('user.phone')
            ->add('description')
            ->add($amountField, 'number')
            ->add('status', 'choice', [
                'choices' => $this->statuses,
                'catalogue' => 'messages'
            ]);
    }

    /**
     * @param RouteCollection $collection
     */
    public function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->clearExcept(['create', 'list', 'show'])
            ->add('cancel', $this->getRouterIdParameter().'/cancel')
            ->add('process', $this->getRouterIdParameter().'/process');
    }
}