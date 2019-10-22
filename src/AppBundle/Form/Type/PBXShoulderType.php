<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\PBX\Shoulder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Form\DataTransformer\PhoneTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToTimestampTransformer;

class PBXShoulderType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('phone')
            ->add('tariff', ChoiceType::class, [
                'choices' => [
                    Shoulder::TARIFF_LOCAL,
                    Shoulder::TARIFF_CITY,
                    Shoulder::TARIFF_MOBILE
                ]
            ])
            ->add('startAt', TextType::class, [
                'required' => false,
                'empty_data' => null
            ])
            ->add('answerAt', TextType::class, [
                'required' => false,
                'empty_data' => null
            ])
            ->add('hangupAt', TextType::class, [
                'required' => false,
                'empty_data' => null
            ])
            ->add('billSec', IntegerType::class)
            ->add('status', ChoiceType::class, [
                'choices' => [
                    Shoulder::STATUS_NO_ANSWER,
                    Shoulder::STATUS_ANSWER,
                    Shoulder::STATUS_BUSY,
                    Shoulder::STATUS_CANCEL
                ]
            ]);

        $builder->get('phone')
            ->addViewTransformer(new PhoneTransformer());

        $builder->get('startAt')
            ->addViewTransformer(new DateTimeToTimestampTransformer());

        $builder->get('answerAt')
            ->addViewTransformer(new DateTimeToTimestampTransformer());

        $builder->get('hangupAt')
            ->addViewTransformer(new DateTimeToTimestampTransformer());
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Shoulder::class);
    }
}