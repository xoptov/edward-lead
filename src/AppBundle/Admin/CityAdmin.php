<?php

namespace AppBundle\Admin;

use AppBundle\Entity\City;
use AppBundle\Entity\Region;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CityAdmin extends AbstractAdmin
{
    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('region.name', null, ['label' => 'Region'])
            ->add("leadPrice")
            ->add("starPrice")
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
            ->add('region.name', null, ['label' => 'Region'])
            ->add("leadPrice")
            ->add("starPrice")
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
            ->add("region", EntityType::class, [
                'label' => 'Region',
                'class' => Region::class,
                'choice_label' => 'name'
            ])
            ->add("name")
            ->add("leadPrice")
            ->add("starPrice")
            ->add("enabled")
        ;
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('region.name', null, ['label' => 'Region'])
            ->add('name')
            ->add("leadPrice")
            ->add("starPrice")
            ->add('enabled')
            ->add('createdAt')
            ->add('updatedAt')
        ;
    }

    /**
     * @param City $object
     * @return string
     */
    public function toString($object): string
    {
        return $object->getName() ?? "новый город";
    }
}