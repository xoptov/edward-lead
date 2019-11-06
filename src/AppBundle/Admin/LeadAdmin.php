<?php

namespace AppBundle\Admin;

use AppBundle\Entity\City;
use AppBundle\Entity\Lead;
use AppBundle\Entity\Property;
use AppBundle\Exception\LeadException;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Form\DataTransformer\PhoneTransformer;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class LeadAdmin extends AbstractAdmin
{
    /**
     * @var array
     */
    protected $accessMapping = [
        'archive' => 'EDIT'
    ];

    /**
     * @inheritdoc
     */
    public function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->add('name')
            ->add('phone', null, ['template' => '@App/CRUD/list_phone_number.html.twig'])
            ->add('createdAt')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'archive' => ['template' => '@App/CRUD/list__action_archive.html.twig']
                ]
            ]);
    }

    /**
     * @inheritdoc
     */
    public function configureFormFields(FormMapper $form)
    {
        $form
            ->add('name', 'text')
            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name'
            ])
            ->add('phone', 'text')
            ->add('channel', EntityType::class, [
                'class' => Property::class,
                'choice_label' => 'value'
            ])
            ->add('orderDate')
            ->add('decisionMaker', ChoiceType::class, [
                'choices' => [
                    'Да' => true,
                    'Нет' => false
                ]
            ])
            ->add('interestAssessment')
            ->add('description');

        $form->get('phone')->addViewTransformer(new PhoneTransformer());
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('room.name')
            ->add('name')
            ->add('city.name', null, ['label' => 'City'])
            ->add('phone', null, ['template' => '@App/CRUD/show_phone_field.html.twig'])
            ->add('channel.value', null, ['label' => 'Lead Channel'])
            ->add('orderDate')
            ->add('decisionMaker', 'boolean')
            ->add('interestAssessment')
            ->add('description');
    }

    /**
     * @inheritDoc
     */
    public function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->clearExcept(['list', 'edit', 'show'])
            ->add('archive', $this->getRouterIdParameter() . '/archive');
    }

    /**
     * @param Lead $object
     *
     * @throws LeadException
     * @throws ModelManagerException
     */
    public function archive($object)
    {
        if (!$object->isExpected()) {
            throw new LeadException($object, 'Лаи не может быть отправлен в архив');
        }

        $object->setStatus(Lead::STATUS_ARCHIVE);
        $this->getModelManager()->update($object);
    }
}
