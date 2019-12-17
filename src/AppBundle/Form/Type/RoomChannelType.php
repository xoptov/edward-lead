<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Property;
use AppBundle\Entity\RoomChannel;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RoomChannelType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('property', EntityType::class, [
                'label' => 'Channel',
                'class' => Property::class,
                'choice_label' => 'value',
                'query_builder' => function(EntityRepository $entityRepository) {
                    $queryBuilder = $entityRepository->createQueryBuilder('p');
                    $queryBuilder
                        ->where('p.type = :channel')
                        ->setParameter('channel', Property::CHANNEL);
                    return $queryBuilder;
                }
            ])
            ->add('allowed', ChoiceType::class, [
                'choices' => [
                    'Да' => 1,
                    'Нет' => 0
                ]
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', RoomChannel::class);
    }
}