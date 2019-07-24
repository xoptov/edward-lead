<?php

namespace AppBundle\Form\Type;


use AppBundle\Entity\Image;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use AppBundle\Form\Type\DataTransformer\EntityToIdTransformer;

class ImageType extends HiddenType
{
    /** @var EntityManager */
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addViewTransformer(new EntityToIdTransformer($this->em, Image::class));
    }
}