<?php


namespace NotificationBundle\Client;


use NotificationBundle\ChannelModel\ChannelInterface;
use NotificationBundle\Exception\ValidationChannelModelException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseClient
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param ChannelInterface $model
     * @throws ValidationChannelModelException
     */
    public function validate(ChannelInterface $model)
    {
        $errors = $this->getValidator()->validate($model);

        if (count($errors) > 0) {
            throw new ValidationChannelModelException($errors);
        }
    }

    /**
     * @return ValidatorInterface
     */
    private function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }
}