<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CompanyType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('shortName', TextType::class, [
                'required' => false
            ])
            ->add('largeName', TextType::class, [
                'required' => false
            ])
            ->add('inn', TextType::class, [
                'required' => false
            ])
            ->add('ogrn', TextType::class, [
                'required' => false
            ])
            ->add('kpp', TextType::class)
            ->add('bik', TextType::class)
            ->add('accountNumber', TextType::class)
            ->add('address', TextareaType::class)
            ->add('zipcode', TextType::class);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Company::class);
    }
}