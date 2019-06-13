<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Lead;
use AppBundle\Entity\User;
use AppBundle\Entity\PhoneCall;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class CallbackType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('call_id', TextType::class, [
                'property_path' => 'externalId'
            ])
            ->add('src_phone_number', EntityType::class, [
                'class' => User::class,
                'property_path' => 'caller',
                'choice_value' => 'phone'
            ])
            ->add('dst_phone_number', EntityType::class, [
                'class' => Lead::class,
                'property_path' => 'lead',
                'choice_value' => 'phone'
            ])
            ->add('duration', NumberType::class, [
                'property_path' => 'durationInSecs'
            ])
            ->add('started_at', DateTimeType::class, [
                'property_path' => 'startedAt'
            ])
            ->add('answer_at', DateTimeType::class, [
                'property_path' => 'answerAt'
            ])
            ->add('completed_at', DateTimeType::class, [
                'property_path' => 'completedAt'
            ])
            ->add('status', TextType::class)
            ->add('billsec', NumberType::class, [
                'property_path' => 'billSecs'
            ])
            ->add('recording', TextType::class, [
                'property_path' => 'record'
            ]);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PhoneCall::class,
            'allow_extra_fields' => true,
            'csrf_protection' => false
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return null;
    }
}