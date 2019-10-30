<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\City;
use AppBundle\Entity\Room;
use AppBundle\Entity\Account;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Form\EventListener\RoomTypeSubscriber;
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
            ]);

        if (isset($options['timer']) && $options['timer']) {
            $builder
                ->add('city', EntityType::class, [
                    'class' => City::class,
                    'choice_label' => 'name',
                    'required' => false
                ])
                ->add('schedule', ScheduleType::class, [
                    'required' => false
                ])
                ->add('executionHours', TextType::class, [
                    'required' => false
                ])
                ->add('leadsPerDay', TextType::class, [
                    'required' => false
                ]);
        }

        $builder->add('submit', SubmitType::class);

        $builder->addEventSubscriber(new RoomTypeSubscriber());
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefined('timer')
            ->setAllowedValues('timer', [false, true])
            ->setDefaults([
                'data_class' => Room::class,
                'timer' => false
            ]);
    }
}