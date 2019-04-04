<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Region;
use AppBundle\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\IsTrue;

class CompanyType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('shortName', TextType::class)
            ->add('largeName', TextType::class)
            ->add('phone', TelType::class)
            ->add('email', EmailType::class)
            ->add('inn', TextType::class)
            ->add('ogrn', TextType::class)
            ->add('kpp', TextType::class)
            ->add('bik', TextType::class)
            ->add('accountNumber', TextType::class)
            ->add('address', TextType::class)
            ->add('zipcode', TextType::class)
            ->add('officeName', TextType::class)
            ->add('officeAddress', TextType::class)
            ->add('officePhone', TelType::class)
            ->add('regions', EntityType::class, [
                'class' => Region::class,
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => true
            ])
            ->add('submit', SubmitType::class)
            ->add('reset', ResetType::class);

        if ($options['creating']) {
            $builder
                ->add('publicationAgree', CheckboxType::class, [
                    'mapped' => false,
                    'constraints' => [
                        new IsTrue(['message' => 'Вы должны дать согласие на публикацию'])
                    ]
                ])
                ->add('storeAgree', CheckboxType::class, [
                    'mapped' => false,
                    'constraints' => [
                        new IsTrue(['message' => 'Вы должны дать согласие на хранение'])
                    ]
                ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Company::class);

        $resolver->setDefined('creating')
            ->setDefault('creating', false)
            ->setAllowedTypes('creating', 'boolean');
    }
}