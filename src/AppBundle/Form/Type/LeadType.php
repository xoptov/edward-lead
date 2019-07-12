<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\City;
use AppBundle\Entity\Lead;
use AppBundle\Entity\Property;
use AppBundle\Form\Type\DataTransformer\PhoneTransformer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class LeadType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name'
            ])
            ->add('phone', TelType::class)
            ->add('channel', EntityType::class, [
                'class' => Property::class,
                'choice_label' => 'value',
                'required' => false,
                'query_builder' => function(EntityRepository $er) {
                    $qb = $er->createQueryBuilder('ch')
                        ->where('ch.type = :type')
                        ->setParameter('type', Property::CHANNEL);

                    return $qb;
                }
            ])
            ->add('orderDate', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy',
                'required' => false
            ])
            ->add('decisionMaker', ChoiceType::class, [
                'expanded' => true,
                'required' => false,
                'choices' => [
                    'Да' => true,
                    'Нет' => false
                ]
            ])
            ->add('madeMeasurement', ChoiceType::class, [
                'expanded' => true,
                'required' => false,
                'choices' => [
                    'Да' => true,
                    'Нет' => false
                ]
            ])
            ->add('interestAssessment', HiddenType::class)
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('uploader', FileType::class, [
                'required' => false,
                'mapped' => false,
                'attr' => ['accept' => 'webm,ogg,mpeg,mp3,wave,wav,flac']
            ])
            ->add('audioRecord', HiddenType::class)
            ->add('publicationRule', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Необходимо согласиться с правилами публикации'])
                ]
            ])
            ->add('hasAgreement', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Необходимо указать что клиент согласен на обработку данных третьими лицами'])
                ]
            ])
            ->add('submit', SubmitType::class);

        $builder->get('phone')->addViewTransformer(new PhoneTransformer());
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Lead::class);
    }
}