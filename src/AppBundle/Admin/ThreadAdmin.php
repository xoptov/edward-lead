<?php

namespace AppBundle\Admin;

use AppBundle\Entity\City;
use AppBundle\Entity\Lead;
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
                'template' => '@App/CRUD/list_thread_participants_field.html.twig'
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
                'class' => 'col-xs-3 col-sm-3 col-md-3',
                'box_class' => 'box box-solid box-default'
            ])
            ->add('id')
            ->add('createdBy.username')
            ->add('participants', null, [
                'template' => '@App/CRUD/show_thread_participants_field.html.twig'
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
            ->end();

        if ($this->subject->hasLead()) {
            $showMapper
                ->with('Lead', [
                    'class' => 'col-xs-4 col-sm-4 col-md-4',
                    'box_class' => 'box box-solid box-default'
                ])
                ->add('lead.id')
                ->add('lead.trade.buyer.name', null, [
                    'label' => 'Lead Buyer'
                ])
                ->add('lead.trade.seller.name', null, [
                    'label' => 'Lead Seller'
                ])
                ->add('lead.trade.createdAt', 'datetime', [
                    'label' => 'Trade CreatedAt',
                    'format' => 'd.m.Y H:i:s'
                ])
                ->add('lead.name')
                ->add('lead.phone', null, [
                    'template' => '@App/CRUD/show_phone_field.html.twig'
                ])
                ->add('lead.orderDate', 'date', [
                    'label' => 'Order Date',
                    'format' => 'd.m.Y'
                ])
                ->add('lead.channel.value', null, [
                    'label' => 'Lead Channel'
                ])
                ->add('lead.decisionMaker')
                ->add('lead.interestAssessment')
                ->add('lead.description')
                ->add('lead.audioRecord', null, [
                    'template' => '@App/CRUD/show_audio_record_field.html.twig'
                ])
                ->end();
        }

        $showMapper
            ->with('Appeal', [
                'class' => 'col-xs-5 col-sm-5 col-md-5',
                'box_class' => 'box box-solid box-default'
            ])
            ->add('subject')
            ->add('messages', 'collection', [
                'template' => '@App/CRUD/show_messages_field.html.twig'
            ])
            ->end();
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