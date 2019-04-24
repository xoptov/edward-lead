<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Invoice;
use AppBundle\Entity\IncomeAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Sonata\AdminBundle\Form\DataTransformer\ModelToIdTransformer;

class InvoiceProcessType extends AbstractType
{
    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @param ModelManager $modelManager
     */
    public function __construct(ModelManager $modelManager)
    {
        $this->modelManager = $modelManager;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('invoice', HiddenType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('incomeAccount', EntityType::class, [
                'class' => IncomeAccount::class,
                'choice_label' => 'description',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('submit', SubmitType::class);

        $builder->get('invoice')
            ->addViewTransformer(new ModelToIdTransformer($this->modelManager, Invoice::class));
    }
}