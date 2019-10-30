<?php

namespace AppBundle\Form\EventListener;

use AppBundle\Entity\City;
use AppBundle\Form\Type\ScheduleType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RoomTypeSubscriber implements EventSubscriberInterface
{
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'preSubmit'
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (isset($data['timer']) && $data['timer']) {
            $form
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
        } else {
            $form->remove('timer');
            unset(
                $data['timer'],
                $data['city'],
                $data['schedule'],
                $data['executionHours'],
                $data['leadsPerDay']
            );
            $event->setData($data);
        }
    }
}