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
use AppBundle\Form\Type\DataTransformer\PhoneTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

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
                ->add('kpp', TextType::class)
                ->add('bik', TextType::class)
                ->add('accountNumber', TextType::class)
                ->add('address', TextareaType::class)
                ->add('zipcode', TextType::class)
                ->add('logotypePath', HiddenType::class, [
                    'required' => false
                ])
                ->add('uploader', FileType::class, [
                    'mapped' => false,
                    'required' => false
                ])
                ->addEventSubscriber(new CompanyTypeSubscriber());

            $builder->get('phone')
                ->addViewTransformer(new PhoneTransformer());

        } else {
            $builder
                ->add('officeName', TextType::class)
                ->add('officeAddress', TextType::class)
                ->add('officePhone', TelType::class);
//                ->add('cities', EntityType::class, [
//                    'class' => City::class,
//                    'choice_label' => 'name',
//                    'expanded' => true,
//                    'multiple' => true
//                ]);

            $builder->get('officePhone')
                ->addViewTransformer(new PhoneTransformer());
        }

        $builder->add('submit', SubmitType::class);
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