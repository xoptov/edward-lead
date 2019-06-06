<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\City;
use AppBundle\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Form\EventListener\CompanyTypeSubscriber;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CompanyType extends AbstractType
{
    const MODE_COMPANY = 'company';
    const MODE_OFFICE = 'office';

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (self::MODE_COMPANY === $options['mode']) {
            $builder
                ->add('shortName', TextType::class)
                ->add('largeName', TextType::class)
                ->add('phone', TelType::class)
                ->add('email', EmailType::class)
                ->add('inn', TextType::class)
                ->add('ogrn', TextType::class)
                ->add('kpp', TextType::class, [
                    'required' => false
                ])
                ->add('bik', TextType::class)
                ->add('accountNumber', TextType::class)
                ->add('address', TextType::class)
                ->add('zipcode', TextType::class)
                ->add('logotypePath', HiddenType::class)
                ->add('uploader', FileType::class)
                ->addEventSubscriber(new CompanyTypeSubscriber());
        } else {
            $builder
                ->add('officeName', TextType::class)
                ->add('officeAddress', TextType::class)
                ->add('officePhone', TelType::class)
                ->add('cities', EntityType::class, [
                    'class' => City::class,
                    'choice_label' => 'name',
                    'expanded' => true,
                    'multiple' => true
                ]);
        }

        $builder->add('submit', SubmitType::class);

//        if ($options['creating']) {
//            $builder
//                ->add('publicationAgree', CheckboxType::class, [
//                    'mapped' => false,
//                    'constraints' => [
//                        new IsTrue(['message' => 'Вы должны дать согласие на публикацию'])
//                    ]
//                ])
//                ->add('storeAgree', CheckboxType::class, [
//                    'mapped' => false,
//                    'constraints' => [
//                        new IsTrue(['message' => 'Вы должны дать согласие на хранение'])
//                    ]
//                ]);
//        }
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Company::class);

        $resolver
            ->setDefined('mode')
            ->setDefault('mode', self::MODE_COMPANY)
            ->setAllowedTypes('mode', 'string')
            ->setAllowedValues('mode', [
                self::MODE_COMPANY,
                self::MODE_OFFICE
            ]);
    }
}