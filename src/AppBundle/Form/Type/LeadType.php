<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\City;
use AppBundle\Entity\Lead;
use AppBundle\Entity\Room;
use AppBundle\Entity\Property;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AppBundle\Form\DataTransformer\EntityToIdTransformer;
use AppBundle\Form\Type\DataTransformer\PhoneTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class LeadType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('room', HiddenType::class)
            ->add('name', TextType::class)
            ->add('city', HiddenType::class)
            ->add('phone', TelType::class)
            ->add('channel', HiddenType::class)
            ->add('orderDate', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy',
                'required' => false
            ])
            ->add('decisionMaker', HiddenType::class)
            ->add('interestAssessment', HiddenType::class)
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('audioRecord', HiddenType::class)
            ->add('hasAgreement', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Необходимо указать что клиент согласен на обработку данных третьими лицами'])
                ]
            ]);

        $builder->get('room')->addViewTransformer(
            new EntityToIdTransformer($this->entityManager, Room::class)
        );

        $builder->get('city')->addViewTransformer(
            new EntityToIdTransformer($this->entityManager, City::class)
        );

        $builder->get('channel')->addViewTransformer(
            new EntityToIdTransformer($this->entityManager, Property::class)
        );

        $builder->get('phone')->addViewTransformer(new PhoneTransformer());
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Lead::class);
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return null;
    }
}