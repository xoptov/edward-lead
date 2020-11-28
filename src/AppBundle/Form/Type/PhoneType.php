<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Form\DataTransformer\PhoneTransformer;
use Symfony\Component\Form\Extension\Core\Type\TelType;

class PhoneType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new PhoneTransformer());
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return TelType::class;
    }
}