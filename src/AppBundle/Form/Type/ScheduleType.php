<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Room\Schedule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AppBundle\Form\DataTransformer\BitMaskToArrayTransformer;

class ScheduleType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('workTime', WorkTimeType::class, [
                'required' => false
            ])
            ->add('workDays', ChoiceType::class, [
                'choices' => [
                    'Пн' => Schedule::MONDAY,
                    'Вт' => Schedule::TUESDAY,
                    'Ср' => Schedule::WEDNESDAY,
                    'Чт' => Schedule::THURSDAY,
                    'Пт' => Schedule::FRIDAY,
                    'Сб' => Schedule::SATURDAY,
                    'Вс' => Schedule::SUNDAY
                ],
                'multiple' => true,
                'expanded' => true,
                'required' => false
            ]);

        $builder
            ->get('workDays')
            ->addModelTransformer(
                new BitMaskToArrayTransformer(Schedule::SUNDAY)
            );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Schedule::class);
    }
}