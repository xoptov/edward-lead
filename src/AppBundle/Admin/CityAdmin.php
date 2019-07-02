<?php

namespace AppBundle\Admin;

use AppBundle\Entity\City;
use AppBundle\Entity\Region;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use AppBundle\Admin\Field\MoneyFieldDescription;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CityAdmin extends AbstractAdmin
{
    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $leadPriceField = new MoneyFieldDescription();
        $leadPriceField->setName('leadPrice');

        $starPriceField = new MoneyFieldDescription();
        $starPriceField->setName('starPrice');

        $datagridMapper
            ->add('id')
            ->add('region.name', null, ['label' => 'Region'])
            ->add($leadPriceField)
            ->add($starPriceField)
            ->add('name', null, ['label' => 'City'])
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
        $leadPriceField = new MoneyFieldDescription();
        $leadPriceField->setName('leadPrice');

        $starPriceField = new MoneyFieldDescription();
        $starPriceField->setName('starPrice');

        $listMapper
            ->addIdentifier('id', 'number')
            ->add('name', null, ['label' => 'City'])
            ->add('region.name', null, ['label' => 'Region'])
            ->add($leadPriceField)
            ->add($starPriceField)
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
        $leadPriceField = new MoneyFieldDescription();
        $leadPriceField->setName('leadPrice');

        $starPriceField = new MoneyFieldDescription();
        $starPriceField->setName('starPrice');

        $formMapper
            ->add("region", EntityType::class, [
                'label' => 'Region',
                'class' => Region::class,
                'choice_label' => 'name'
            ])
            ->add("name", null, ['label' => 'City'])
            ->add($leadPriceField)
            ->add($starPriceField)
            ->add("enabled")
        ;
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $leadPriceField = new MoneyFieldDescription();
        $leadPriceField->setName('leadPrice');

        $starPriceField = new MoneyFieldDescription();
        $starPriceField->setName('starPrice');

        $showMapper
            ->add('id')
            ->add('region.name', null, ['label' => 'Region'])
            ->add('name', null, ['label' => 'City'])
            ->add($leadPriceField)
            ->add($starPriceField)
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