<?php

namespace AppBundle\Admin;

use AppBundle\Entity\User;
use AppBundle\Service\UserManager;
use AppBundle\Service\AccountManager;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use AppBundle\Admin\Field\MoneyFieldDescription;
use AppBundle\Admin\Field\LastLoginAtFieldDescription;
use AppBundle\Admin\Field\AccountHoldFieldDescription;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class UserAdmin extends AbstractAdmin
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var AccountManager
     */
    private $accountManager;

    /**
     * @param UserManager $userManager
     */
    public function setUserManager(UserManager $userManager): void
    {
        $this->userManager = $userManager;
    }

    /**
     * @param AccountManager $accountManager
     */
    public function setAccountManager(AccountManager $accountManager): void
    {
        $this->accountManager = $accountManager;
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
        $lastLoginAtField = new LastLoginAtFieldDescription();
        $lastLoginAtField->setName('lastLoginAt');

        $balanceField = new MoneyFieldDescription();
        $balanceField->setName('balance');
        $balanceField->setFieldName('humanBalance');

        $holdAmountField = new AccountHoldFieldDescription($this->accountManager);
        $holdAmountField->setName('hold');
        $holdAmountField->setFieldName('account');

        $listMapper
            ->addIdentifier('id', 'number')
            ->add('name')
            ->add('phone', null, ['template' => '@App/CRUD/list_phone_number.html.twig'])
            ->add('email')
            ->add('roles', 'array', ['template' => '@App/CRUD/list_array.html.twig'])
            ->add('enabled')
            ->add('createdAt', 'datetime', [
                'format' => 'd.m.Y H:i:s'
            ])
            ->add($lastLoginAtField, 'datetime', [
                'format' => 'd.m.Y H:i:s'
            ])
            ->add($balanceField)
            ->add($holdAmountField)
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
