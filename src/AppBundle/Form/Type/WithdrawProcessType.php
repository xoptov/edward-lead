<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Withdraw;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\OutgoingAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sonata\AdminBundle\Form\DataTransformer\ModelToIdTransformer;

class WithdrawProcessType extends AbstractType
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
//                    new NotBlank()
                ]
            ])
            ->add('account', EntityType::class, [
                'class' => OutgoingAccount::class,
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->where('a.enabled = :enabled')
                        ->setParameter('enabled', true);
                },
                'choice_label' => 'description',
                'constraints' => [
//                    new NotBlank()
                ]
            ])
            ->add('submit', SubmitType::class);

        $builder->get('invoice')
            ->addViewTransformer(new ModelToIdTransformer($this->modelManager, Withdraw::class));
    }
}
