<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\User;
use AppBundle\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Физлицо' => User::TYPE_PERSONAL,
                    'Юрлицо' => User::TYPE_COMPANY
                ],
                'required' => false
            ])
            ->add('email', EmailType::class)
            ->add('name', TextType::class)
            ->add('phone', PhoneType::class)
            ->add('skype', TextType::class, [
                'required' => false
            ])
            ->add('vkontakte', TextType::class, [
                'required' => false
            ])
            ->add('facebook', TextType::class, [
                'required' => false
            ])
            ->add('telegram', TextType::class, [
                'required' => false
            ])
            ->add('logotype', EntityType::class, [
                'class' => Image::class,
                'required' => false
            ])
            ->add('personal', PersonalType::class, [
                'required' => false
            ]);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', User::class);
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return null;
    }
}