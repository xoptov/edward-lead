<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Thread;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

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
            ->add('participants', null, [
                'template' => '@App/CRUD/list_thread_metadata_participants.html.twig'
            ])
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
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'reply' => ['template' => '@App/CRUD/list__action_reply.html.twig'],
                    'writeToSeller' => ['template' => '@App/CRUD/list__action_write_to_seller.html.twig'],
                    'close' => ['template' => '@App/CRUD/list__action_close.html.twig'],
                ],
            ])
        ;
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General', [
                'class' => 'col-xs-12 col-sm-4 col-md-3',
                'box_class' => 'box box-solid box-default'
            ])
                ->add('id')
                ->add('createdBy.username')
                ->add('participants', null, [
                    'template' => '@App/CRUD/show_thread_metadata_participants.html.twig'
                ])
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
            ->end()
            ->with('Appeal', [
                'class' => 'col-xs-12 col-sm-8 col-md-9',
                'box_class' => 'box box-solid box-default'
            ])
                ->add('subject')
                ->add('messages', 'collection', [
                    'template' => '@App/CRUD/show_messages_field.html.twig'
                ])
            ->end()
        ;
    }

    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->clearExcept(['list', 'show'])
            ->add('reply',$this->getRouterIdParameter().'/reply')
            ->add('writeToSeller',$this->getRouterIdParameter().'/write_to_seller')
            ->add('close',$this->getRouterIdParameter().'/close')
        ;
    }

    /**
     * @param Thread $object
     * @return string
     */
    public function toString($object)
    {
        return '#' . $object->getId() . ' ' . $object->getSubject() ?? "";
    }
}