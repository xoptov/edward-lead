<?php

namespace AppBundle\Admin;

use AppBundle\Service\UserManager;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Doctrine\ORM\OptimisticLockException;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

class UserAdmin extends AbstractAdmin
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @param UserManager $userManager
     */
    public function setUserManager(UserManager $userManager): void
    {
        $this->userManager = $userManager;
    }

    /**
     * @param $object
     * @throws OptimisticLockException
     */
    public function prePersist($object)
    {
        $this->userManager->updateUser($object, false);
    }

    /**
     * @param $object
     * @throws OptimisticLockException
     */
    public function preUpdate($object)
    {
        $this->userManager->updateUser($object, false);
    }

    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('phone')
            ->add('email')
            ->add('roles')
            ->add('enabled')
            ->add('typeSelected')
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
            ->add('phone')
            ->add('email')
            ->add('skype')
            ->add('telegram')
            ->add('roles')
            ->add('enabled')
            ->add('typeSelected')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => []
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
            ->add('name')
            ->add('plainPassword', 'password', ['label' => 'Password'])
            ->add('phone')
            ->add('email')
            ->add('skype')
            ->add('vkontakte')
            ->add('facebook')
            ->add('telegram')
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
            ->add('name')
            ->add('phone')
            ->add('email')
            ->add('skype')
            ->add('vkontakte')
            ->add('facebook')
            ->add('telegram')
            ->add('roles')
            ->add('enabled')
            ->add('typeSelected')
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
            'show'
        ]);
    }
}
