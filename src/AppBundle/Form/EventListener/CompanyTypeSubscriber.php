<?php

namespace AppBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CompanyTypeSubscriber implements EventSubscriberInterface
{
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        if (!$event->getData()) {

            $form = $event->getForm();

            $form->add('storeAgree', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue(['message' => 'Вы должны дать согласие на хранение'])
                ]
            ]);
        }
    }
}