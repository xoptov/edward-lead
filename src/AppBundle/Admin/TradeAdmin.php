<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Lead;
use AppBundle\Entity\Trade;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

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
                    Trade::STATUS_NEW => 'new',
                    Trade::STATUS_ACCEPTED => 'accepted',
                    Trade::STATUS_REJECTED => 'rejected',
                    Trade::STATUS_PROCEEDING => 'proceeding'
                ],
                'catalogue' => 'messages'
            ])
            ->add('_action', null, [
                'actions' => [
                    'accept' => ['template' => '@App/CRUD/list__action_accept.html.twig'],
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
            ->add('lead', null, [
                'template' => '@App/CRUD/show_lead_url_field.html.twig'
            ])
            ->add('lead.name')
            ->add('lead.channel.value')
            ->add('lead.description')
            ->add('lead.createdAt')
            ->add('lead.decisionMaker', 'choice', [
                'choices' => [
                    Lead::DECISION_MAKER_UNKNOWN => 'Неизвестно',
                    Lead::DECISION_MAKER_YES => 'Да',
                    Lead::DECISION_MAKER_NO => 'Нет'
                ]
            ])
            ->add('lead.interestAssessment')
            ->add('status', 'choice', [
                'choices' => [
                    Trade::STATUS_NEW => 'new',
                    Trade::STATUS_ACCEPTED => 'accepted',
                    Trade::STATUS_REJECTED => 'rejected',
                    Trade::STATUS_PROCEEDING => 'proceeding'
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
                        'new' => Trade::STATUS_NEW,
                        'accepted' => Trade::STATUS_ACCEPTED,
                        'rejected' => Trade::STATUS_REJECTED,
                        'proceeding' => Trade::STATUS_PROCEEDING
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
            ->add('accept', $this->getRouterIdParameter() . '/accept')
            ->add('reject', $this->getRouterIdParameter() . '/reject');
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