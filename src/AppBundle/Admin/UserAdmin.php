<?php

namespace AppBundle\Admin;

use AppBundle\Entity\User;
use AppBundle\Service\UserManager;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Doctrine\ORM\OptimisticLockException;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

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
     * @inheritdoc
     */
    public function prePersist($object)
    {
        $this->userManager->updateUser($object, false);
    }

    /**
     * @inheritdoc
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
            ->add("saleLeadLimit")
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
            ->add('roles', "array", ['template' => '@App/CRUD/list_array.html.twig'])
            ->add("saleLeadLimit")
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
            ->add('plainPassword', 'password', [
                'label' => 'Password',
                'required' => false
            ])
            ->add('phone')
            ->add('email')
            ->add('skype')
            ->add('vkontakte')
            ->add('facebook')
            ->add('telegram')
            ->add("saleLeadLimit", NumberType::class)
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
            ->add("saleLeadLimit")
            ->add('enabled')
            ->add('typeSelected')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('referrer', null, [
                'template' => '@App/CRUD/show_referrer_field.html.twig'
            ])
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

    /**
     * @param User $object
     * @return string
     */
    public function toString($object)
    {
        return $object->getName() ?? "новый пользователь";
    }
}
