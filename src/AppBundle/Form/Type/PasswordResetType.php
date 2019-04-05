<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class PasswordResetType extends AbstractType
{
    const STEP_FIRST = 'step_1';
    const STEP_SECOND = 'step_2';

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (self::STEP_FIRST === $options['step']) {
            $builder->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Необходимо указать Email']),
                    new Email(['message' => 'Невалидное значение Email'])
                ]
            ]);
        } else {
            $builder
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'constraints' => [
                        new NotBlank(['message' => 'Пароль должен быть указан'])
                    ],
                    'invalid_message' => 'Пароли должны совпадать',
                    'first_options' => [
                        'label' => 'Новый пароль'
                    ],
                    'second_options' => [
                        'label' => 'Повторите пароль'
                    ]
                ])
                ->add('resetToken', HiddenType::class);
        }

        $builder->add('submit', SubmitType::class);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined('step')
            ->setDefault('step', self::STEP_FIRST)
            ->setAllowedValues('step', [self::STEP_FIRST, self::STEP_SECOND]);
    }
}