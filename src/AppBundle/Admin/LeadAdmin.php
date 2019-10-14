<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Lead;
use AppBundle\Exception\LeadException;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Exception\ModelManagerException;

class LeadAdmin extends AbstractAdmin
{
    /**
     * @var array
     */
    protected $accessMapping = [
        'archive' => 'EDIT'
    ];

    /**
     * @inheritDoc
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
                    'archive' => ['template' => '@App/CRUD/list__action_archive.html.twig']
                ]
            ]);
    }

    /**
     * @inheritDoc
     */
    public function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->clearExcept(['list'])
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
