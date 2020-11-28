<?php

namespace AppBundle\Admin;

use AppBundle\Entity\User;
use AppBundle\Entity\Withdraw;
use AppBundle\Service\HoldManager;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use AppBundle\Admin\Field\MoneyFieldDescription;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Form\DataTransformer\MoneyTransformer;
use AppBundle\Admin\Field\DestinationAccountFieldDescription;

class WithdrawAdmin extends AbstractAdmin
{
    /**
     * @var array
     */
    private $statuses = [
        Withdraw::STATUS_NEW => 'new',
        Withdraw::STATUS_DONE => 'done',
        Withdraw::STATUS_REJECTED => 'rejected',
        Withdraw::STATUS_CANCELED => 'canceled'
    ];

    /**
     * @var HoldManager
     */
    private $holdManager;

    /**
     * @param HoldManager $holdManager
     */
    public function setHoldManager(HoldManager $holdManager): void
    {
        $this->holdManager = $holdManager;
    }

    /**
     * @param Withdraw $object
     */
    public function prePersist($object)
    {
        $hold = $this->holdManager->create(
            $object->getAccount(),
            $object,
            $object->getAmount(),
            false
        );

        $object->setHold($hold);
    }

    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $amountField = new MoneyFieldDescription();
        $amountField->setName('amount');

        $datagridMapper
            ->add('id')
            ->add('description')
            ->add($amountField)
            ->add('createdAt')
            ->add('updatedAt');
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $amountField = new MoneyFieldDescription();
        $amountField->setName('amount');

        $destinationAccountField = new DestinationAccountFieldDescription();
        $destinationAccountField->setName('destinationAccount');

        $listMapper
            ->addIdentifier('id', 'number')
            ->add('user.id')
            ->add('description')
            ->add($amountField, 'number')
            ->add('status', 'choice', [
                'choices' => $this->statuses,
                'catalogue' => 'messages',
                'template' => '@App/CRUD/list_status.html.twig'
            ])
            ->add('createdAt', 'datetime', ['format' => 'd.m.Y H:i:s'])
            ->add('updatedAt', 'datetime', ['format' => 'd.m.Y H:i:s'])
            ->add('confirm.createdAt', 'datetime', [
                'label' => 'Confirm Created At',
                'format' => 'd.m.Y H:i:s']
            )
            ->add($destinationAccountField, null, [
                'label' => 'Output Method'
            ])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'cancel' => ['template' => '@App/CRUD/list__action_cancel.html.twig'],
                    'process' => ['template' => '@App/CRUD/list__action_process.html.twig']
                ],
            ]);
    }

    /**
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name'
            ])
            ->add('description')
            ->add('amount', 'number');

        $formMapper->get('amount')
            ->addModelTransformer(new MoneyTransformer());

    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $amountField = new MoneyFieldDescription();
        $amountField->setName('amount');

        $showMapper
            ->add('id', 'number')
            ->add('user.id')
            ->add('user.name')
            ->add('user.email')
            ->add('user.phone', null, ['template' => '@App/CRUD/show_phone_field.html.twig'])
            ->add('description')
            ->add($amountField, 'number')
            ->add('status', 'choice', [
                'choices' => $this->statuses
            ])
            ->add('createdAt', 'datetime', ['format' => 'd.m.Y H:i:s'])
            ->add('updatedAt', 'datetime', ['format' => 'd.m.Y H:i:s']);
    }

    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['create', 'list', 'show']);

        $collection->add('cancel', $this->getRouterIdParameter().'/cancel');
        $collection->add('process', $this->getRouterIdParameter().'/precess ');
    }
}
