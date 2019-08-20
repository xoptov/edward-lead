<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Trade;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

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
                    0 => 'new',
                    1 => 'accepted',
                    2 => 'rejected',
                    3 => 'arbitrage'
                ],
                'catalogue' => 'messages'
            ])
            ->add('_action', null, [
                'actions' => [
                    'reject' => ['template' => '@App/CRUD/list__action_reject.html.twig'],
                    'show' => []
                ]
            ]);
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('buyer.name')
            ->add('seller.name')
            ->add('lead.name')
            ->add('lead.channel.value')
            ->add('lead.createdAt')
            ->add('lead.description')
            ->add('lead.decisionMaker')
            ->add('lead.madeMeasurement')
            ->add('lead.interestAssessment')
            ->add('status', 'choice', [
                'choices' => [
                    0 => 'new',
                    1 => 'accepted',
                    2 => 'rejected',
                    3 => 'arbitrage'
                ],
                'catalogue' => 'messages'
            ])
            ->add('lead.audioRecord', null, ['template' => '@App/CRUD/show_audio_field.html.twig'])
        ;
    }

    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('buyer.name')
            ->add('seller.name')
            ->add('lead.name')
            ->add('status',
                null,
                [],
                'choice',
                [
                    'choices' => [
                        'new' => 0,
                        'accepted' => 1,
                        'rejected' => 2,
                        'arbitrage' => 3
                    ],
                    'choice_translation_domain' => 'messages'
                ]
            )
        ;
    }

    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list', 'show'])
            ->add('reject', $this->getRouterIdParameter().'/reject');
    }

    /**
     * @param Trade $object
     * @return string
     */
    public function toString($object)
    {
        return $object->getId() ? 'Сделка ' . $object->getId() : 'новая сделка';
    }
}