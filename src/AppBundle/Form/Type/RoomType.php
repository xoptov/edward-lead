<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\City;
use AppBundle\Entity\Room;
use AppBundle\Entity\Account;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RoomType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('sphere', TextType::class)
            ->add('leadCriteria', TextareaType::class)
            ->add('leadPrice', MoneyType::class, [
                'currency' => 'RUB',
                'divisor' => Account::DIVISOR
            ])
            ->add('platformWarranty', ChoiceType::class, [
                'data' => true,
                'choices' => [
                    'Да' => true,
                    'Нет' => false
                ],
                'expanded' => true
            ])
            ->add('timer', ChoiceType::class, [
                'data' => false,
                'choices' => [
                    'Да' => true,
                    'Нет' => false
                ],
                'expanded' => true
            ])
            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name',
                'required' => false
            ])
            ->add('schedule', ScheduleType::class, [
                'required' => false
            ])
            ->add('executionHours', NumberType::class, [
                'required' => false
            ])
            ->add('leadsPerDay', NumberType::class, [
                'required' => false
            ])
            ->add('submit', SubmitType::class);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Room::class);
    }
}