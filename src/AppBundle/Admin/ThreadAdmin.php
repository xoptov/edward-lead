<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Thread;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class ThreadAdmin extends AbstractAdmin
{
    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('status',
                'doctrine_orm_string',
                [],
                'choice',
                [
                    'choices' => [
                        'New' => Thread::STATUS_NEW,
                        'Waiting for user response' => Thread::STATUS_WAIT_USER,
                        'Waiting for support response' => Thread::STATUS_WAIT_SUPPORT,
                        'Closed' => Thread::STATUS_CLOSED
                    ],
                    'choice_translation_domain' => 'messages'
                ]
            )
            ->add('typeAppeal',
                'doctrine_orm_string',
                [],
                'choice',
                [
                    'choices' => [
                        'Arbitration' => Thread::TYPE_ARBITRATION,
                        'Support' => Thread::TYPE_SUPPORT
                    ],
                    'choice_translation_domain' => 'messages'
                ]
            )
            ->add('createdAt', 'doctrine_orm_datetime_range', [], null, [
                'field_type' => 'sonata_type_date_picker',
                'field_options' => [
                    'format' => 'dd.MM.yyyy',
                    'attr' => [
                        'placeholder' => 'Type date in format 01.01.2019'
                    ]
                ],
                'translation_domain' => 'messages'
            ])
        ;
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->add('createdBy.username')
            ->add('subject')
            ->add('status', 'choice', [
                'choices' => [
                    Thread::STATUS_NEW => 'New',
                    Thread::STATUS_WAIT_USER => 'Waiting for user response',
                    Thread::STATUS_WAIT_SUPPORT => 'Waiting for support response',
                    Thread::STATUS_CLOSED => 'Closed'
                ],
                'catalogue' => 'messages'
            ])
            ->add('typeAppeal', 'choice', [
                'choices' => [
                    Thread::TYPE_ARBITRATION => 'Arbitration',
                    Thread::TYPE_SUPPORT => 'Support'
                ],
                'catalogue' => 'messages'
            ])
            ->add('createdAt')
        ;
    }

    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept([
            'create', // ToDo Удалить после теста
            //'edit',
            'list',
            //'show'
        ]);
    }
}