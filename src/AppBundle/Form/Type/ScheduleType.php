<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\City;
use AppBundle\Entity\Room\Schedule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ScheduleType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name'
            ])
            ->add('startHour', ChoiceType::class, [
                'choices' => $this->getHoursChoices()
            ])
            ->add('endHour', ChoiceType::class, [
                'choices' => $this->getHoursChoices()
            ])
            ->add('daysOfWeek', ChoiceType::class, [
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
                'expanded' => true
            ])
            ->add('executionHours', TextType::class)
            ->add('leadsPerDay', TextType::class);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Schedule::class);
    }

    /**
     * @return array
     */
    private function getHoursChoices(): array
    {
        $items = [];
        for ($x = 0; $x < 24; $x++) {
            $key = sprintf('%02d:00', $x);
            $items[$key] = $x;
        }
        return $items;
    }
}