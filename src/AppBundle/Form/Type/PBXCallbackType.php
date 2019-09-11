<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\PhoneCall;
use AppBundle\Entity\PBX\Callback;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AppBundle\Form\EventListener\PBXCallbackTypeSubscriber;

class PBXCallbackType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('phoneCall', EntityType::class, [
                'class' => PhoneCall::class,
                'choice_value' => 'externalId'
            ])
            ->add('event', TextType::class)
            ->add('firstShoulder', PBXShoulderType::class)
            ->add('secondShoulder', PBXShoulderType::class)
            ->add('audioRecord', TextType::class);

        $builder->addEventSubscriber(new PBXCallbackTypeSubscriber($options['fields_map']));
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('data_class', Callback::class)
            ->setDefined('fields_map')
            ->setRequired('fields_map')
            ->setAllowedTypes('fields_map', ['array']);
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return null;
    }
}