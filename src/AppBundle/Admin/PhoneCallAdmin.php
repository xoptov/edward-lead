<?php

namespace AppBundle\Admin;

use AppBundle\Entity\PhoneCall;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use AppBundle\Admin\Field\MoneyFieldDescription;

class PhoneCallAdmin extends AbstractAdmin
{
    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $amount = new MoneyFieldDescription();
        $amount->setName('amount');

        $filter
            ->add('externalId')
            ->add('trade.lead.phone')
            ->add('status',null, [], 'choice', [
                'choices' => [
                    PhoneCall::STATUS_NEW => 'Новый',
                    PhoneCall::STATUS_REQUESTED => 'Запрошен',
                    PhoneCall::STATUS_PROCESSED => 'Обработан',
                    PhoneCall::STATUS_ERROR => 'Ошибка'
                ]
            ])
            ->add($amount);
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $list)
    {
        $amount = new MoneyFieldDescription();
        $amount->setName('amount');

        $list
            ->addIdentifier('id')
            ->add('externalId')
            ->add('caller.id')
            ->add('caller.name')
            ->add('caller.company.officePhone', null, [
                'label' => 'Caller Phone'
            ])
            ->add('trade.lead.id', null, [
                'label' => 'Lead Id'
            ])
            ->add('trade.lead.name', null, [
                'label' => 'Lead Name'
            ])
            ->add('trade.lead.phone', null, [
                'label' => 'Lead Phone'
            ])
            ->add('status', 'choice', [
                'choices' => [
                    PhoneCall::STATUS_NEW => 'Новый',
                    PhoneCall::STATUS_REQUESTED => 'Запрошен',
                    PhoneCall::STATUS_PROCESSED => 'Обработан',
                    PhoneCall::STATUS_ERROR => 'Ошибка'
                ]
            ])
            ->add($amount)
            ->add('getLastAudioRecord', null, [
                'label' => 'Recording',
                'template' => '@App/CRUD/list_recording.html.twig'
            ]);
    }

    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept('list');
    }
}