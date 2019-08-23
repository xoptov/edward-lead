<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Thread;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use AppBundle\Form\DataTransformer\EntityToIdTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ReplayType extends AbstractType
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
        $builder
            ->add('thread', HiddenType::class)
            ->add('body', TextareaType::class, [
                'constraints' => [new NotBlank(['message' => 'Необходимо указать текст сообщения'])]
            ])
            ->add('images', CollectionType::class, [
                'entry_type' => ImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'required' => false
            ])
            ->add('file', FileType::class, [
                'mapped' => false
            ])
        ;

        $builder->get('thread')->addViewTransformer(
            new EntityToIdTransformer($this->em, Thread::class)
        );
    }

    /**
     * @return null|string
     */
    public function getBlockPrefix()
    {
        return null;
    }
}