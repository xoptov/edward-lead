<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Country;
use AppBundle\Entity\Region;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RegionAdmin extends AbstractAdmin
{
    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('country.name', null, ['label' => 'Country'])
            ->add('name')
            ->add('enabled')
            ->add('createdAt')
            ->add('updatedAt')
        ;
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', 'number')
            ->add('name')
            ->add('country.name', null, ['label' => 'Country'])
            ->add('enabled')
            ->add('createdAt')
            ->add('updatedAt')
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
            ->add('country', EntityType::class, [
                'label' => 'Country',
                'class' => Country::class,
                'choice_label' => 'name'
            ])
            ->add('name')
            ->add('enabled')
        ;
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('country.name', null, ['label' => 'Country'])
            ->add('name')
            ->add('enabled')
            ->add('createdAt')
            ->add('updatedAt')
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
     * @param Region $object
     * @return string
     */
    public function toString($object): string
    {
        return $object->getName() ?? "новый регион";
    }
}
