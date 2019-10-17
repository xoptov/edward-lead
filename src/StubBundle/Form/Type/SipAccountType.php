<?php

namespace StubBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class SipAccountType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('login', null, [
                'constraints' => [
                    new NotBlank(['message' => 'Необходимо указать login'])
                ]
            ])
            ->add('password', null, [
                'constraints' => [
                    new NotBlank(['message' => 'Необходимо указать password'])
                ]
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