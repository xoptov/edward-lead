<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\User\Personal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('personal', Personal::class);
    }
}