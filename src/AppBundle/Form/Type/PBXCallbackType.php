<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\PhoneCall;
use AppBundle\Entity\PBXCallback;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class PBXCallbackType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('event', ChoiceType::class, [
                'choices' => [
                    'hangup' => PBXCallback::EVENT_HANGUP
                ]
            ])
            ->add('call_id', EntityType::class, [
                'property_path' => 'phoneCall',
                'class' => PhoneCall::class,
                'choice_value' => 'externalId'
            ])
            ->add('src_phone_number', TextType::class, [
                'property_path' => 'srcPhoneNumber'
            ])
            ->add('dst_phone_number', TextType::class, [
                'property_path' => 'dstPhoneNumber'
            ])
            ->add('direction', TextType::class)
            ->add('duration', NumberType::class)
            ->add('started_at', DateTimeType::class, [
                'property_path' => 'startedAt',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm:ss'
            ])
            ->add('answer_at', DateTimeType::class, [
                'property_path' => 'answerAt',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm:ss'
            ])
            ->add('completed_at', DateTimeType::class, [
                'property_path' => 'completedAt',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm:ss'
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'status_answer'    => PBXCallback::STATUS_ANSWER,
                    'status_busy'      => PBXCallback::STATUS_BUSY,
                    'status_no_answer' => PBXCallback::STATUS_NO_ANSWER
                ]
            ])
            ->add('billsec', NumberType::class, [
                'property_path' => 'billsec'
            ])
            ->add('recording', TextType::class);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PBXCallback::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true
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