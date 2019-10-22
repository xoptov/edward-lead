<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Property;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

class PropertyAdmin extends AbstractAdmin
{
    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('type',
                'doctrine_orm_string',
                [],
                'choice', [
                    'choices' => [
                        'Channel' => Property::CHANNEL
                    ]
                ])
            ->add('value')
        ;
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('type', 'choice', [
                'choices' => [
                    Property::CHANNEL => 'Channel'
                ],
                'catalogue' => 'messages'
            ])
            ->add('value')
            ->add('_action', null, [
                'actions' => [
                    'edit' => [],
                    'show' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    /**
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('type', 'choice', [
                'choices' => [
                    'Channel' => Property::CHANNEL
                ],
                'choice_translation_domain' => 'messages'
            ])
            ->add('value')
        ;
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('type', 'choice', [
                'choices' => [
                    Property::CHANNEL => 'Channel'
                ],
                'catalogue' => 'messages'
            ])
            ->add('value')
        ;
    }

    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept([
            'create',
            'edit',
            'list',
            'show',
            'delete'
        ]);
    }

    /**
     * @param Property $object
     * @return string
     */
    public function toString($object): string
    {
        return $object->getValue() ?? 'новое свойство';
    }
}